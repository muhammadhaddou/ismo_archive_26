<?php

return [

    'title' => 'ISMO Archive',
    'title_prefix' => '',
    'title_postfix' => ' | ISMO',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    'logo' => '<b>ISMO</b> Archive',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'ISMO Logo',

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'ISMO Logo',
            'width' => 50,
            'height' => 50,
        ],
    ],

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'ISMO Preloader',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,

    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_topnav' => 'navbar-white navbar-light',

    'sidebar_mini' => 'lg',

    'use_route_url' => true,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',

    'menu' => [

        // Navbar
        [
            'type' => 'navbar-search',
            'text' => 'Rechercher',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar search
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Rechercher',
        ],

        // Dashboard
        [
            'text' => 'Tableau de bord',
            'url'  => 'dashboard',
            'icon' => 'fas fa-tachometer-alt',
        ],

        // STAGIAIRES
        ['header' => 'GESTION DES STAGIAIRES'],
        [
            'text' => 'Stagiaires',
            'icon' => 'fas fa-users',
            'submenu' => [
                [
                    'text' => 'Liste des stagiaires',
                    'url'  => 'trainees',
                    'icon' => 'fas fa-list',
                ],
                [
                    'text' => 'Ajouter',
                    'url'  => 'trainees/create',
                    'icon' => 'fas fa-user-plus',
                ],
                [
                    'text' => 'Importer Excel',
                    'url'  => 'trainees/import',
                    'icon' => 'fas fa-file-excel',
                ],
            ],
        ],

        // DOCUMENTS
        ['header' => 'GESTION DES DOCUMENTS'],
        [
            'text' => 'Baccalauréat',
            'icon' => 'fas fa-graduation-cap',
            'submenu' => [
                [
                    'text' => 'Liste',
                    'url'  => 'documents/bac',
                ],
                [
                    'text' => 'Retraits temporaires',
                    'url'  => 'documents/bac/temp-out',
                    'icon' => 'fas fa-clock',
                    'label' => '!',
                    'label_color' => 'warning',
                ],
            ],
        ],
        [
            'text' => 'Diplômes',
            'icon' => 'fas fa-certificate',
            'submenu' => [
                [
                    'text' => 'Liste',
                    'url'  => 'documents/diplome',
                ],
                [
                    'text' => 'Prêts',
                    'url'  => 'documents/diplome/prets',
                    'icon' => 'fas fa-check-circle',
                ],
            ],
        ],
        [
            'text' => 'Bulletins',
            'url'  => 'documents/bulletin',
            'icon' => 'fas fa-file-alt',
        ],
        [
            'text' => 'Attestations',
            'url'  => 'documents/attestation',
            'icon' => 'fas fa-file-contract',
        ],

        // MOUVEMENTS
        ['header' => 'MOUVEMENTS'],
        [
            'text' => 'Historique',
            'url'  => 'movements',
            'icon' => 'fas fa-exchange-alt',
        ],
        [
            'text' => 'Aujourd’hui',
            'url'  => 'movements/today',
            'icon' => 'fas fa-calendar-day',
        ],

        // VALIDATIONS
        ['header' => 'VALIDATIONS'],
        [
            'text' => 'Registre',
            'url'  => 'validations',
            'icon' => 'fas fa-check-double',
        ],

        // ADMINISTRATION
        ['header' => 'ADMINISTRATION'],

        [
            'text' => 'Utilisateurs',
            'url'  => 'users',
            'icon' => 'fas fa-fw fa-user-cog',
            'can'  => 'manage-users',
        ],

        [
            'text'    => 'Paramètres',
            'icon'    => 'fas fa-fw fa-cogs',
            'can'     => 'manage-users',
            'submenu' => [
                [
                    'text' => 'Secteurs',
                    'url'  => 'secteurs',
                    'icon' => 'fas fa-fw fa-building',
                ],
                [
                    'text' => 'Filières',
                    'url'  => 'filieres',
                    'icon' => 'fas fa-fw fa-code-branch',
                ],
            ],
        ],
    ],

    'plugins' => [

        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],

        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],

        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],

        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],

        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

];