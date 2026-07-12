<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EntrepriseSeeder extends Seeder
{
    /**
     * Reprend les valeurs qui vivaient jusqu'ici dans config/company.php
     * (et le nom + logo qui étaient codés en dur dans les vues) pour peupler
     * la ligne singleton. Les futurs clients modifieront ces valeurs depuis
     * le panneau admin, pas via ce seeder.
     */
    public function run(): void
    {
        $entreprise = Entreprise::firstOrCreate(['id' => 1], [
            'name' => config('company.name', 'Mboup Gaming'),
            'email' => config('company.email'),
            'phone' => config('company.phone'),
            'whatsapp_number' => config('company.whatsapp_number'),
            'address_line1' => config('company.address_line1'),
            'address_line2' => config('company.address_line2'),
            'country' => 'Sénégal',
            'ninea' => config('company.ninea'),
            'rccm' => config('company.rc'),
            'currency' => 'XOF',
            'accent_color' => '#1e3a5f',
        ]);

        if (! $entreprise->logo_path) {
            $sourcePath = public_path('images/logo.jpeg');

            if (is_file($sourcePath)) {
                $disk = Storage::disk('public');
                $destination = 'entreprise/logo.jpeg';
                $disk->put($destination, file_get_contents($sourcePath));
                $entreprise->update(['logo_path' => $destination]);
            }
        }
    }
}
