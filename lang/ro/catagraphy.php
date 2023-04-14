<?php

declare(strict_types=1);

return [

    'label' => [
        'singular' => 'catagrafie',
        'plural' => 'catagrafii',
    ],

    'header' => [
        'vulnerabilities' => 'Sumar vulnerabilități',
        'recommendations' => 'Recomandări',
    ],

    'footer' => [
        'updated_at' => 'Catagrafie realizată în :created_at, modificată ultima dată in :updated_at, de :name',
        'action' => 'Vezi istoric modificări',
    ],

    'action' => [
        'create' => 'Adaugă catagrafie',
        'edit' => 'Editează catagrafie',
        'update' => 'Actualizează catagrafie',
        'delete' => 'Șterge catagrafie',
    ],

    'vulnerability' => [
        'none' => 'Fără :vulnerability',
        'empty' => [
            'title' => 'Vulnerabilități indisponibile',
            'description' => 'Realizează catagrafia beneficiarului, pentru a putea identifica vulnerabilitățile acestuia.',
            'create' => 'Catagrafiază beneficiar',
        ],

        'socioeconomic' => 'Vulnerabilități socio-economice',
        'health' => 'Vulnerabilități de sănătate',
        'reproductive_health' => 'Sănătate reproductivă',
    ],

    'recommendation' => [
        'empty' => [
            'title' => 'Recomandări indisponibile',
            'description' => 'Sistemul îți va  face unele recomandări de intervenții, în funcție de vulnerabilitățile beneficiarului. Momentan nu avem niciuna identificată.',
        ],
    ],

    'section' => [
        'general' => 'Informații generale',
        'notes' => 'Observații finale',
    ],
];
