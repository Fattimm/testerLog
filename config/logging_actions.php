<?php

return [
    'App\Http\Controllers\UserController' => [
        'createUser' => [
            'action' => 'Création utilisateur',
            'details' => [
                'created_user_id',
                'created_user_name',
                'error_message',
                'failed_email',
                'validation_errors',
                'submitted_data'
            ],
            'success_message' => 'Utilisateur créé avec succès',
            'error_message' => 'Échec de création utilisateur'
        ],
        'listUsers' => [
            'action' => 'Liste des utilisateurs',
            'details' => [
                'total_users',
                'error_message',
                'pagination_info'
            ],
            'success_message' => 'Liste des utilisateurs récupérée',
            'error_message' => 'Échec de récupération des utilisateurs'
        ],
        'deleteUser' => [
            'action' => 'Suppression utilisateur',
            'details' => [
                'deleted_user_id', 
                'deleted_user_name', 
                'error_message', 
                'failed_user_id',
                'attempted_user_id',
                'validation_errors',
                'submitted_data'
            ],
            'success_message' => 'Utilisateur supprimé avec succès',
            'error_message' => 'Échec de suppression utilisateur'
        ],
    ],
    'App\Http\Controllers\AuthController' => [
        'login' => [
            'action' => 'Tentative de connexion',
            'details' => [
                'email', 
                'user_id', 
                'error',
                'validation_errors',
                'submitted_data',
                'ip_address',
                'user_agent',
                'attempt_time'
            ],
            'success_message' => 'Connexion réussie',
            'error_message' => 'Échec de connexion'
        ],
        'logout' => [
            'action' => 'Déconnexion',
            'details' => [
                'user_id', 
                'email',
                'ip_address',
                'session_duration',
                'error_message'
            ],
            'success_message' => 'Déconnexion réussie',
            'error_message' => 'Échec de déconnexion'
        ],
    ],
    'App\Http\Controllers\LogController' => [
        'index' => [
            'action' => 'Consultation des logs',
            'details' => [
                'total_logs', 
                'current_page', 
                'per_page', 
                'error_message',
                'filter_params',
                'sort_params',
                'date_range',
                'validation_errors',
                'submitted_data',
                'query_duration'
            ],
            'success_message' => 'Liste des logs récupérée',
            'error_message' => 'Échec de récupération des logs'
        ],
        'show' => [
            'action' => 'Consultation d\'un log spécifique',
            'details' => [
                'log_id',
                'error_message',
                'validation_errors',
                'submitted_data'
            ],
            'success_message' => 'Log récupéré avec succès',
            'error_message' => 'Échec de récupération du log'
        ],
        'export' => [
            'action' => 'Export des logs',
            'details' => [
                'total_exported',
                'date_range',
                'filter_params',
                'export_format',
                'file_size',
                'error_message',
                'validation_errors',
                'submitted_data'
            ],
            'success_message' => 'Logs exportés avec succès',
            'error_message' => 'Échec de l\'export des logs'
        ]
    ]
];
