<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Balance;
use App\Models\Condominium;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AdminAndCondoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Yosmar',
            'email' => 'admin@test.com',
            'password' => '123456',
            'role' => 'administrator',
        ]);

        $administrator = Administrator::create([
            'user_id' => $user->id,
        ]);

        // Create a condominium
        $condo = Condominium::create([
            'name' => 'Condominio de Prueba',
            'administrator_id' => $administrator->id,
            'address' => 'Calle 123',
            'city' => 'Caracas',
            'state' => 'Distrito Capital',
            'country' => 'Venezuela',
            'postal_code' => '1010',
            'phone' => '0212-1234567',
            'name_to_invoice' => 'Condominio de Prueba',
        ]);

        // start date for balance 30 days ago
        $start_date = Carbon::now()->subDays(30);

        // Create initial balance for the condominium
        $balance = Balance::create([
            'condominium_id' => $condo->id,
            'balance' => 500,
            'created_at' => $start_date,
        ]);

        // Create 3 unit types for the condominium
        $unitType1 = UnitType::create([
            'condominium_id' => $condo->id,
            'name' => 'Esquina',
            'description' => 'Apartamento 86mts',
            'percentage' => 0.25,
        ]);

        $unitType2 = UnitType::create([
            'condominium_id' => $condo->id,
            'name' => 'Medio',
            'description' => 'Apartamento 72mts',
            'percentage' => 0.20,
        ]);

        $unitType3 = UnitType::create([
            'condominium_id' => $condo->id,
            'name' => 'Penthouse',
            'description' => 'Apartamento 120mts',
            'percentage' => 0.30,
        ]);

        $faker = Faker::create();

        // Create 3 units for the condominium
        $unit1 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_type_id' => $unitType1->id,
            'unit_number' => 'PB-1',
            'owner_name' => $faker->name,
            'owner_phone' => $faker->phoneNumber,
            'owner_email' => $faker->email,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        $unit2 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_type_id' => $unitType2->id,
            'unit_number' => 'PB-2',
            'owner_name' => $faker->name,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        $unit3 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_type_id' => $unitType3->id,
            'unit_number' => 'PB-3',
            'owner_name' => $faker->name,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        // Create 2 expense categories for the condominium
        $expenseCategory1 = ExpenseCategory::create([
            'condominium_id' => $condo->id,
            'name' => 'Gastos Generales de la Urbanización',
            'description' => 'Gastos generales de la urbanización',
        ]);

        $expenseCategory2 = ExpenseCategory::create([
            'condominium_id' => $condo->id,
            'name' => 'Gastos de Mantenimiento',
            'description' => 'Gastos de mantenimiento del condominio',
        ]);

        $startDate = $start_date;
        $currentDate = $startDate;

        // Get 10 expenses descriptions
        $expensesDescriptions = [
            'Pago de vigilancia',
            'Pago de limpieza',
            'Pago de jardinería',
            'Pago de administración',
            'Pago de ascensor',
            'Pago de electricidad',
            'Pago de agua',
            'Pago de gas',
            'Pago de teléfono',
            'Pago de internet',
        ];

        foreach (range(1, 28) as $index) {
            // Altern between expenses and incomes
            if ($index % 2 === 0) {
                $date = $currentDate->addDays(1)->format('Y-m-d');

                // Create expense for category 1 or 2
                $expense = Expense::create([
                    'condominium_id' => $condo->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'expense_category_id' => $faker->randomElement([$expenseCategory1->id, $expenseCategory2->id]),
                    'description' => $faker->randomElement($expensesDescriptions),
                    'date' => $date,
                    'created_at' => $date,
                ]);

                // Get the balance of the unit to update it
                $balance = Balance::where('condominium_id', $condo->id)->latest()->first();

                // Update the balance of the unit
                $balance = Balance::create([
                    'condominium_id' => $condo->id,
                    'expense_id' => $expense->id,
                    'balance' => $balance->balance - $expense->amount,
                    'created_at' => $date,
                ]);
            } else {
                // Select a random unit
                $unit = $faker->randomElement([$unit1, $unit2, $unit3]);

                // Date for the income
                $date = $currentDate->addDays(1)->format('Y-m-d');

                // Get a random description for the income
                $incomesDescriptions = [
                    'Pago de mantenimiento',
                    'Pago de condominio',
                    'Pago de vigilancia',
                    'Pago de limpieza',
                    'Pago de jardinería',
                    'Pago de administración',
                    'Pago de ascensor',
                ];

                // Create income
                $income = Income::create([
                    'condominium_id' => $condo->id,
                    'unit_id' => $unit->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'description' => $faker->randomElement($incomesDescriptions),
                    'method' => 'transfer',
                    'bank' => 'Banesco',
                    'date' => $date,
                    'created_at' => $date,
                ]);

                // Get the balance of the unit from unit-balance to update it
                $unit->balance + $income->amount;

                // Update the balance of the condominium
                $balance = Balance::where('condominium_id', $condo->id)->latest()->first();

                // Update the balance of the condominium
                $balance = Balance::create([
                    'condominium_id' => $condo->id,
                    'income_id' => $income->id,
                    'balance' => $balance->balance + $income->amount,
                    'created_at' => $date,
                ]);
            }
        }
    }
}
