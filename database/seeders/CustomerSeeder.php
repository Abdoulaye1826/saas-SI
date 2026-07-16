<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /** @var list<string> */
    private const FIRST_NAMES_M = [
        'Mamadou', 'Moussa', 'Ibrahima', 'Cheikh', 'Ousmane', 'Abdoulaye', 'Modou',
        'Serigne', 'El Hadji', 'Babacar', 'Amadou', 'Alioune', 'Souleymane', 'Pape', 'Malick',
    ];

    /** @var list<string> */
    private const FIRST_NAMES_F = [
        'Aissatou', 'Fatou', 'Awa', 'Khady', 'Astou', 'Ndeye', 'Mariama', 'Bineta',
        'Ramatoulaye', 'Coumba', 'Aminata', 'Sokhna', 'Adja', 'Marième', 'Anta',
    ];

    /** @var list<string> */
    private const LAST_NAMES = [
        'Diop', 'Ndiaye', 'Fall', 'Sarr', 'Ba', 'Gueye', 'Diagne', 'Sow', 'Cissé',
        'Diallo', 'Faye', 'Sy', 'Thiam', 'Kane', 'Seck', 'Mbaye', 'Touré', 'Camara', 'Dieng', 'Niang',
    ];

    /** @var list<string> */
    private const NEIGHBORHOODS = [
        'Plateau', 'Médina', 'Grand Yoff', 'Parcelles Assainies', 'Sacré-Cœur',
        'Liberté 6', 'Ouakam', 'Ngor', 'Yoff', 'Point E', 'Mermoz', 'Fann',
        'HLM', 'Grand Dakar', 'Ouest Foire',
    ];

    /** @var list<string> */
    private const CITIES = ['Dakar', 'Guédiawaye', 'Pikine', 'Rufisque', 'Thiès', 'Mbour', 'Saint-Louis'];

    public function run(): void
    {
        $usedNames = [];

        for ($i = 0; $i < 32; $i++) {
            $isMale = random_int(0, 1) === 0;
            $firstName = $isMale
                ? self::FIRST_NAMES_M[array_rand(self::FIRST_NAMES_M)]
                : self::FIRST_NAMES_F[array_rand(self::FIRST_NAMES_F)];
            $lastName = self::LAST_NAMES[array_rand(self::LAST_NAMES)];
            $fullName = "{$firstName} {$lastName}";

            // Évite les doublons exacts pour ne pas fausser la recherche client.
            if (isset($usedNames[$fullName])) {
                $lastName = self::LAST_NAMES[array_rand(self::LAST_NAMES)];
                $fullName = "{$firstName} {$lastName}";
            }
            $usedNames[$fullName] = true;

            $neighborhood = self::NEIGHBORHOODS[array_rand(self::NEIGHBORHOODS)];
            $city = self::CITIES[array_rand(self::CITIES)];
            $phone = sprintf('+221 7%d %s %s %s',
                random_int(0, 8),
                str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT),
                str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT),
                str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT)
            );

            Customer::firstOrCreate(
                ['full_name' => $fullName, 'phone' => $phone],
                [
                    'email' => random_int(0, 100) < 40
                        ? strtolower(str_replace(' ', '.', \Illuminate\Support\Str::ascii($fullName))) . '@gmail.com'
                        : null,
                    'address' => "Quartier {$neighborhood}",
                    'city' => $city,
                    'registered_at' => now()->subDays(random_int(5, 400)),
                ]
            );
        }
    }
}
