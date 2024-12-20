<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get Condominium id
        $condominium_id = Auth::user()->condominium->id;

        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator' && $condominium_id) {
            // Get all invoices
            $invoices = Invoice::where('condominium_id', $condominium_id)->get();

            //Map all invoices and convert month from (01 to Enero) in spanish
            $invoices = $invoices->map(function ($invoice) {
                $months = [
                    '01' => 'Enero',
                    '02' => 'Febrero',
                    '03' => 'Marzo',
                    '04' => 'Abril',
                    '05' => 'Mayo',
                    '06' => 'Junio',
                    '07' => 'Julio',
                    '08' => 'Agosto',
                    '09' => 'Septiembre',
                    '10' => 'Octubre',
                    '11' => 'Noviembre',
                    '12' => 'Diciembre',
                ];

                $invoice->month = $months[$invoice->month];

                return $invoice;
            });

            // Return a JSON response with the invoices
            return response()->json(
                $invoices,
                200
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get Condominium id
        $condominium_id = Auth::user()->condominium->id;

        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator' && $condominium_id) {
            // Create a new invoice
            $invoice = Invoice::create([
                'description' => $request->description,
                'amount' => $request->amount,
                'reserve_fund' => $request->reserve_fund,
                'total_amount' => $request->total_amount,
                'condominium_id' => $condominium_id,
                'month' => $request->month,
                'year' => $request->year,
                'due_date' => $request->due_date,
            ]);

            // Add invoice items
            foreach ($request->expenses as $expenseId) {
                $invoice->invoiceItems()->create([
                    'expense_id' => $expenseId,
                ]);
            }

            // Change all expenses to invoiced
            foreach ($request->expenses as $expenseId) {
                $expense = Expense::find($expenseId);
                $expense->invoiced = true;
                $expense->save();
            }

            // Return a JSON response with the condominium
            return response()->json([
                'message' => 'Successfully created invoice',
                'invoice' => $invoice,
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // Get Condominium id
        $condominium_id = Auth::user()->condominium->id;

        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator' && $condominium_id) {
            // Get the invoice
            $invoice = Invoice::where('condominium_id', $condominium_id)->where('id', $invoice->id)->first();

            //Map all expenses to get categories of each expense and organize them by category
            $categories = $invoice->expenses->map(function ($expense) {
                return $expense->category->name;
            })->unique();

            // Organize expenses by category
            $categories = $categories->map(function ($category) use ($invoice) {
                return [
                    'category' => $category,
                    'expenses' => $invoice->expenses->filter(function ($expense) use ($category) {
                        return $expense->category->name === $category;
                    }),
                ];
            });

            // Remove expenses from invoice
            $invoice->expenses = null;

            // Return a JSON response with the invoice
            return response()->json(
                [
                    'invoice' => $invoice,
                    'categories' => $categories,
                ],
                200
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function dataToInvoice()
    {
        // Verify if user id is role administrator
        if (Auth::user()->role === 'administrator') {
            // Get the condominium
            $condominium = Auth::user()->administrator->condominium;

            // Expenses where invoiced is false
            $expenses = $condominium->expenses()
                ->where('invoiced', false)
                ->get()->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'name' => $expense->description . ' - ' . date('d/m/Y', strtotime($expense->created_at)) . ' - ' . $expense->amount . '$',
                        'description' => $expense->description,
                        'amount' => $expense->amount,
                        'created_at' => $expense->created_at,
                        'category' => $expense->category->name,
                    ];
                });

            $units_types = $condominium->unitTypes;

            // Prepare to only get name and percentage from unit type
            $units_types = $units_types->map(function ($unit_type) {
                return [
                    'name' => $unit_type->name,
                    'percentage' => $unit_type->percentage,
                ];
            });

            // Prepare data to Only show condominium name_to_invoice, phone and email
            $condominium = [
                'id' => $condominium->id,
                'name_to_invoice' => $condominium->name_to_invoice,
                'phone' => $condominium->phone,
                'email' => Auth::user()->email,
                'image_url' => $condominium->image_url,
            ];

            // Return a JSON response with the condominiums
            return response()->json(
                [
                    'condominium' => $condominium,
                    'expenses' => $expenses,
                    'units_types' => $units_types,
                ],
                200
            );
        } else {
            return response()->json([
                'message' => 'You are not authorized to view condominiums',
            ], 401);
        }
    }

    private function getDataForPDF($invoiceId)
    {
        // Get the invoice
        $invoice = Invoice::where('id', $invoiceId)->first();

        //Map all expenses to get categories of each expense and organize them by category
        $categories = $invoice->expenses->map(function ($expense) {
            return $expense->category->name;
        })->unique();

        // Organize expenses by category
        $categories = $categories->map(function ($category) use ($invoice) {
            return [
                'category' => $category,
                'expenses' => $invoice->expenses->filter(function ($expense) use ($category) {
                    return $expense->category->name === $category;
                }),
            ];
        });

        //Map invoice to remove expenses
        $invoice->expenses = '';

        // Return a JSON response with the invoice
        return [
            'invoice' => $invoice,
            'categories' => $categories,
        ];
    }

    public function generatePDF($invoiceId)
    {

        $data = $this->getDataForPDF('9d3f682e-aadd-431a-90da-d41f777d162e');

        // Generar el PDF
        $pdf = FacadePdf::loadView('invoice', $data);

        // Ruta donde se guardará el archivo en la carpeta public
        $fileName = "invoices/invoice-{$invoiceId}.pdf";
        $filePath = public_path($fileName);

        // Guardar el archivo en la carpeta public
        $pdf->save($filePath);

        // Devolver el PDF como respuesta
        /*         return response()->stream(
            function () use ($pdf) {
                echo $pdf->output();
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="invoice.pdf"',
            ]
        ); */

        return true;

        // Alternativamente, para previsualizar en el navegador:
        // return $pdf->stream();
    }
}
