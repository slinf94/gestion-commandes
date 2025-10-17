<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Name
    |--------------------------------------------------------------------------
    |
    | This option defines the default log name that will be used when you
    | perform an activity. You can change this value to any string you want.
    |
    */
    'default_log_name' => env('ACTIVITY_LOG_NAME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Default Auth Driver
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication driver that will be used
    | to determine the user that caused the activity. You can change this
    | value to any authentication driver you want.
    |
    */
    'default_auth_driver' => null,

    /*
    |--------------------------------------------------------------------------
    | Subject Morph Map
    |--------------------------------------------------------------------------
    |
    | You may specify the fully qualified class name of the subject model
    | in this array if it is not in the same namespace as the default
    | model namespace.
    |
    */
    'subject_return_type' => 'array',

    /*
    |--------------------------------------------------------------------------
    | Activity Log Model
    |--------------------------------------------------------------------------
    |
    | This option defines the model that will be used to store activity logs.
    | You can change this value to any model you want.
    |
    */
    'activity_model' => \App\Models\ActivityLog::class,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | This option defines the table name that will be used to store activity
    | logs. You can change this value to any table name you want.
    |
    */
    'table_name' => 'activity_log',

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This option defines the database connection that will be used to store
    | activity logs. You can change this value to any connection you want.
    |
    */
    'database_connection' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Clean Up
    |--------------------------------------------------------------------------
    |
    | This option defines whether old activity logs should be cleaned up
    | automatically. You can change this value to any boolean you want.
    |
    */
    'cleanup' => [
        'enabled' => env('ACTIVITY_LOG_CLEANUP_ENABLED', true),
        'days' => env('ACTIVITY_LOG_CLEANUP_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields
    |--------------------------------------------------------------------------
    |
    | This option defines the fields that should be hidden when logging
    | activities. These fields will be replaced with asterisks in the logs.
    |
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'remember_token',
        'api_token',
        'secret',
        'key',
        'token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Log Names
    |--------------------------------------------------------------------------
    |
    | This option defines the different log names that can be used to
    | categorize activities. You can add more log names as needed.
    |
    */
    'log_names' => [
        'default' => 'Activité générale',
        'user' => 'Gestion des utilisateurs',
        'product' => 'Gestion des produits',
        'order' => 'Gestion des commandes',
        'auth' => 'Authentification',
        'system' => 'Système',
        'admin' => 'Administration',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Descriptions
    |--------------------------------------------------------------------------
    |
    | This option defines the default descriptions for different activity
    | types. You can customize these descriptions as needed.
    |
    */
    'descriptions' => [
        'created' => 'créé',
        'updated' => 'modifié',
        'deleted' => 'supprimé',
        'restored' => 'restauré',
        'logged_in' => 'connecté',
        'logged_out' => 'déconnecté',
        'password_changed' => 'mot de passe modifié',
        'profile_updated' => 'profil mis à jour',
    ],
];


