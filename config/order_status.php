<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Order Status Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des statuts de commande avec leurs traductions françaises
    | et leurs propriétés d'affichage.
    |
    */

    'statuses' => [
        'pending' => [
            'text' => 'En attente',
            'class' => 'warning',
            'icon' => '⏳',
            'color' => '#FF9800',
            'description' => 'Commande en attente de confirmation'
        ],
        'confirmed' => [
            'text' => 'Confirmée',
            'class' => 'info',
            'icon' => '✅',
            'color' => '#2196F3',
            'description' => 'Commande confirmée et en cours de préparation'
        ],
        'processing' => [
            'text' => 'En cours de traitement',
            'class' => 'info',
            'icon' => '⚙️',
            'color' => '#2196F3',
            'description' => 'Commande en cours de préparation'
        ],
        'shipped' => [
            'text' => 'Expédiée',
            'class' => 'primary',
            'icon' => '🚚',
            'color' => '#9C27B0',
            'description' => 'Commande expédiée et en cours de livraison'
        ],
        'delivered' => [
            'text' => 'Livrée',
            'class' => 'success',
            'icon' => '🎉',
            'color' => '#4CAF50',
            'description' => 'Commande livrée avec succès'
        ],
        'cancelled' => [
            'text' => 'Annulée',
            'class' => 'danger',
            'icon' => '❌',
            'color' => '#F44336',
            'description' => 'Commande annulée'
        ],
        'completed' => [
            'text' => 'Terminée',
            'class' => 'success',
            'icon' => '✅',
            'color' => '#4CAF50',
            'description' => 'Commande terminée'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Workflow
    |--------------------------------------------------------------------------
    |
    | Définit les transitions possibles entre les statuts
    |
    */

    'workflow' => [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'cancelled'],
        'delivered' => ['completed'],
        'cancelled' => [],
        'completed' => []
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Groups
    |--------------------------------------------------------------------------
    |
    | Groupes de statuts pour faciliter les requêtes
    |
    */

    'groups' => [
        'active' => ['pending', 'confirmed', 'processing', 'shipped'],
        'completed' => ['delivered', 'completed'],
        'cancelled' => ['cancelled'],
        'in_progress' => ['confirmed', 'processing', 'shipped']
    ]
];

