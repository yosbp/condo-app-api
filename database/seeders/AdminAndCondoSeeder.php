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

        // start date for balance 01/05/2024
        $start_date = '2024-01-05';

        // Create initial balance for the condominium
        $balance = Balance::create([
            'condominium_id' => $condo->id,
            'balance' => 500,
            'created_at' => $start_date,
        ]);

        $faker = Faker::create();

        // Create 3 units for the condominium
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
            // Altern between expenses and incomes
            if ($index % 2 === 0) {
                $date = $currentDate->addDays(1)->format('Y-m-d');

                // Create expense
                $expense = Expense::create([
                    'condominium_id' => $condo->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'description' => $faker->sentence,
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

                // Create income
                $income = Income::create([
                    'condominium_id' => $condo->id,
                    'unit_id' => $unit->id,
                    'amount' => $faker->randomFloat(2, 10, 50),
                    'description' => $faker->sentence,
                    'date' => $date,
                    'created_at' => $date,
                ]);

                // Get the balance of the unit to update it
                $unitIncome = Unit::find($income->unit_id);
                $unitIncome->balance = $unitIncome->balance + $income->amount;

                // Update the balance of the unit
                $balance = Balance::where('condominium_id', $condo->id)->latest()->first();

                // Update the balance of the unit
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
