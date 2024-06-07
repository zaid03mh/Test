<?php

namespace Database\Seeders;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;  // Assurez-vous que cette ligne est bien au début du fichier sous le namespace


class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            Employee::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'birthdate' => $faker->date($format = 'Y-m-d', $max = '2004-12-31'), // Ensure age is suitable for work
                'hire_date' => $faker->date($format = 'Y-m-d', $max = '2022-12-31'),
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->numerify('##########'), // Generates a phone number with 10 digits
            ]);
        }
    }
}