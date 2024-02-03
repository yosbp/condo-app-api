<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Balance;
use App\Models\Condominium;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Unit;
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

        // Crear un condominio para el administrador
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

        // start date for balance 01/05/2024
        $start_date = '2024-01-05';

        // Crear Balance inicial del condominio
        $balance = Balance::create([
            'condominium_id' => $condo->id,
            'balance' => 500,
            'created_at' => $start_date,
        ]);

        $faker = Faker::create();

        // Crear 3 unidades para el condominio
        $unit1 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_number' => 'PB-1',
            'owner_name' => $faker->name,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        $unit2 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_number' => 'PB-2',
            'owner_name' => $faker->name,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        $unit3 = Unit::create([
            'condominium_id' => $condo->id,
            'unit_number' => 'PB-3',
            'owner_name' => $faker->name,
            'balance' => 0,
            'type' => 'apartment',
        ]);

        $startDate = Carbon::createFromFormat('d/m/Y', '05/01/2024');
        $currentDate = $startDate;

        foreach (range(1, 28) as $index) {
            // Alternar entre crear un gasto y un ingreso
            if ($index % 2 === 0) {
                // Crear gastos cada dia manteniendo secuencia con ingresos
                $date = $currentDate->addDays(1)->format('Y-m-d');

                // Crear Gasto pero por orden de fecha
                $expense = Expense::create([
                    'condominium_id' => $condo->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'description' => $faker->sentence,
                    'date' => $date,
                    'created_at' => $date,
                ]);

                // obtener el balance anterior del condominio
                $balance = Balance::where('condominium_id', $condo->id)->latest()->first();

                // Actualizar balance del condominio
                $balance = Balance::create([
                    'condominium_id' => $condo->id,
                    'expense_id' => $expense->id,
                    'balance' => $balance->balance - $expense->amount,
                    'created_at' => $date,
                ]);
            } else {
                // Crear ingreso
                $unit = $faker->randomElement([$unit1, $unit2, $unit3]);

                // Crear ingresos cada 2 dia
                $date = $currentDate->addDays(1)->format('Y-m-d');

                $income = Income::create([
                    'condominium_id' => $condo->id,
                    'unit_id' => $unit->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'description' => $faker->sentence,
                    'date' => $date,
                    'created_at' => $date,
                ]);

                // Obtener la unidad del income para actualizar el balance
                $unitIncome = Unit::find($income->unit_id);
                $unitIncome->balance = $unitIncome->balance + $income->amount;

                // obtener el balance anterior del condominio
                $balance = Balance::where('condominium_id', $condo->id)->latest()->first();

                // Actualizar balance del condominio
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
