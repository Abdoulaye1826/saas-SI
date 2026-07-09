<?php

return [
    'name' => env('COMPANY_NAME', 'GAPS APPLE'),

    'email' => env('COMPANY_EMAIL', 'Nlamine85@gmail.com'),

    // Numéro affiché (avec indicatif visible) et numéro WhatsApp officiel de l'entreprise.
    // Les deux représentent la même ligne ; whatsapp_number est au format international
    // sans "+" ni espaces, tel qu'attendu par les liens wa.me.
    'phone' => env('COMPANY_PHONE', '+221 76 365 17 63'),
    'whatsapp_number' => env('COMPANY_WHATSAPP_NUMBER', '221763651763'),

    'address_line1' => env('COMPANY_ADDRESS_LINE1', 'Médina Rue 29 x Blaise Diagne'),
    'address_line2' => env('COMPANY_ADDRESS_LINE2', 'Dakar, Sénégal'),

    'ninea' => env('COMPANY_NINEA', '012188547/1A1'),
    'rc' => env('COMPANY_RC', 'SN.DKR.2025.A.21351'),
];
