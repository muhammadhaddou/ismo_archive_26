<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trainee;
use App\Models\Filiere;
use App\Models\Document;
use App\Models\Movement;

class DemoMoroccanSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['Mohammed', 'Fatima', 'Youssef', 'Amina', 'Karim', 'Khadija', 'Rachid', 'Sara', 'Yassine', 'Salma', 'Mehdi', 'Soukaina', 'Oussama', 'Ibtissam', 'Zakaria', 'Hajar', 'Hicham', 'Meryem', 'Ayoub', 'Zineb', 'Omar', 'Imane', 'Hamza', 'Sanae', 'Younes', 'Nissrine', 'Ilyas', 'Bouchra', 'Anas', 'Nadia'];
        $lastNames = ['Alaoui', 'El Idrissi', 'Bennani', 'Tazi', 'Amrani', 'Lahlou', 'Chraibi', 'Benjelloun', 'Naciri', 'Bennis', 'El Ahmadi', 'El Fassi', 'Berrada', 'Zahiri', 'El Amrani', 'Saidi', 'Filali', 'El Ouarti', 'Bouzid', 'Daoudi'];

        $filieres = Filiere::all();
        if ($filieres->count() === 0) {
            $this->command->info("Please run FiliereSeeder first, or create some filieres.");
            return;
        }

        for ($i = 0; $i < 150; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];

            $trainee = Trainee::create([
                'filiere_id'      => $filieres->random()->id,
                'cin'             => strtoupper(fake()->bothify('?')) . strtoupper(fake()->bothify('?')) . fake()->numerify('######'),
                'cef'             => strtoupper(fake()->bothify('?')) . fake()->numerify('#########'),
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'date_naissance'  => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
                'phone'           => '06' . fake()->numerify('########'),
                'group'           => fake()->randomElement(['G1', 'G2', 'G3', 'G4']),
                'graduation_year' => fake()->randomElement([2022, 2023, 2024, 2025]),
            ]);

            // Create a Bac document for the trainee
            $bac = Document::create([
                'trainee_id'       => $trainee->id,
                'type'             => 'Bac',
                'status'           => fake()->randomElement(['Stock', 'Temp_Out', 'Final_Out']),
                'reference_number' => strtoupper(fake()->bothify('BAC-####')),
                'level_year'       => null,
            ]);

            Movement::create([
                'document_id'  => $bac->id,
                'user_id'      => 1, // Admin user
                'action_type'  => 'Saisie',
                'date_action'  => now(),
                'observations' => 'Importation Demo',
            ]);

            // Sometimes add a diplome
            if (fake()->boolean(60)) {
                $diplome = Document::create([
                    'trainee_id'       => $trainee->id,
                    'type'             => 'Diplome',
                    'status'           => fake()->randomElement(['Stock', 'Final_Out']),
                    'reference_number' => strtoupper(fake()->bothify('DIP-####')),
                    'level_year'       => null,
                ]);

                Movement::create([
                    'document_id'  => $diplome->id,
                    'user_id'      => 1,
                    'action_type'  => 'Saisie',
                    'date_action'  => now(),
                    'observations' => 'Importation Demo',
                ]);
            }
        }

        $this->command->info("150 stagiaires marocains créés avec succès pour la démo !");
    }
}
