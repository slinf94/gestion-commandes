<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages de Validation en Français
    |--------------------------------------------------------------------------
    |
    | Messages personnalisés pour les erreurs de validation en français
    | pour l'interface d'administration Allo Mobile
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté quand :other est :value.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres, des tirets et des traits de soulignement.',
    'alpha_num' => 'Le champ :attribute doit contenir uniquement des chiffres et des lettres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute ne doit contenir que des caractères alphanumériques et des symboles à un octet.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit contenir entre :min et :max éléments.',
        'file' => 'Le champ :attribute doit être entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit être entre :min et :max.',
        'string' => 'Le champ :attribute doit être entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal décimales.',
    'declined' => 'Le champ :attribute doit être décliné.',
    'declined_if' => 'Le champ :attribute doit être décliné quand :other est :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'Le champ :attribute a des dimensions d\'image non valides.',
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer par l\'un des éléments suivants : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'un des éléments suivants : :values.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par l\'un des éléments suivants : :values.',
    'enum' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'exists' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'extensions' => 'Le champ :attribute doit avoir l\'une des extensions suivantes : :values.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
        'file' => 'Le champ :attribute doit être supérieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'string' => 'Le champ :attribute doit être supérieur à :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir :value éléments ou plus.',
        'file' => 'Le champ :attribute doit être supérieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être supérieur ou égal à :value caractères.',
    ],
    'hex_color' => 'Le champ :attribute doit être une couleur hexadécimale valide.',
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value éléments.',
        'file' => 'Le champ :attribute doit être inférieur à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'string' => 'Le champ :attribute doit être inférieur à :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :value éléments.',
        'file' => 'Le champ :attribute doit être inférieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être inférieur ou égal à :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
        'file' => 'Le champ :attribute ne doit pas dépasser :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne doit pas avoir plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'Le champ :attribute doit être d\'au moins :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit être d\'au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit être absent.',
    'missing_if' => 'Le champ :attribute doit être absent quand :other est :value.',
    'missing_unless' => 'Le champ :attribute doit être absent sauf si :other est :value.',
    'missing_with' => 'Le champ :attribute doit être absent quand :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être absent quand :values sont présents.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'not_regex' => 'Le format du champ :attribute n\'est pas valide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une lettre majuscule et une lettre minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le champ :attribute donné est apparu dans une fuite de données. Veuillez choisir un autre :attribute.',
    ],
    'present' => 'Le champ :attribute doit être présent.',
    'present_if' => 'Le champ :attribute doit être présent quand :other est :value.',
    'present_unless' => 'Le champ :attribute doit être présent sauf si :other est :value.',
    'present_with' => 'Le champ :attribute doit être présent quand :values est présent.',
    'present_with_all' => 'Le champ :attribute doit être présent quand :values sont présents.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit quand :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other est dans :values.',
    'prohibits' => 'Le champ :attribute interdit :other d\'être présent.',
    'regex' => 'Le format du champ :attribute n\'est pas valide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if' => 'Le champ :attribute est obligatoire quand :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire quand :other est accepté.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire quand :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire quand aucun des :values n\'est présent.',
    'same' => 'Le champ :attribute et :other doivent correspondre.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size éléments.',
        'file' => 'Le champ :attribute doit être de :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit être de :size.',
        'string' => 'Le champ :attribute doit être de :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'un des éléments suivants : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Le champ :attribute est déjà utilisé.',
    'uploaded' => 'Le champ :attribute n\'a pas pu être téléchargé.',
    'uppercase' => 'Le champ :attribute doit être en majuscules.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Messages Personnalisés pour Allo Mobile
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'nom' => [
            'required' => 'Le nom est obligatoire.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne peut pas dépasser :max caractères.',
        ],
        'prenom' => [
            'required' => 'Le prénom est obligatoire.',
            'string' => 'Le prénom doit être une chaîne de caractères.',
            'max' => 'Le prénom ne peut pas dépasser :max caractères.',
        ],
        'email' => [
            'required' => 'L\'adresse email est obligatoire.',
            'email' => 'L\'adresse email doit être valide.',
            'unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
        ],
        'numero_telephone' => [
            'required' => 'Le numéro de téléphone est obligatoire.',
            'string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'max' => 'Le numéro de téléphone ne peut pas dépasser :max caractères.',
            'unique' => 'Ce numéro de téléphone est déjà utilisé par un autre utilisateur.',
        ],
        'numero_whatsapp' => [
            'string' => 'Le numéro WhatsApp doit être une chaîne de caractères.',
            'max' => 'Le numéro WhatsApp ne peut pas dépasser :max caractères.',
        ],
        'quartier' => [
            'string' => 'Le quartier doit être une chaîne de caractères.',
            'max' => 'Le quartier ne peut pas dépasser :max caractères.',
        ],
        'localisation' => [
            'string' => 'La localisation doit être une chaîne de caractères.',
            'max' => 'La localisation ne peut pas dépasser :max caractères.',
        ],
        'role' => [
            'required' => 'Le rôle est obligatoire.',
            'in' => 'Le rôle doit être : client, admin ou gestionnaire.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut doit être : pending, active, suspended ou inactive.',
        ],
        'password' => [
            'required' => 'Le mot de passe est obligatoire.',
            'string' => 'Le mot de passe doit être une chaîne de caractères.',
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
        ],
        'name' => [
            'required' => 'Le nom du produit est obligatoire.',
            'string' => 'Le nom du produit doit être une chaîne de caractères.',
            'max' => 'Le nom du produit ne peut pas dépasser :max caractères.',
        ],
        'price' => [
            'required' => 'Le prix est obligatoire.',
            'numeric' => 'Le prix doit être un nombre.',
            'min' => 'Le prix doit être d\'au moins :min.',
        ],
        'stock_quantity' => [
            'required' => 'La quantité en stock est obligatoire.',
            'integer' => 'La quantité en stock doit être un nombre entier.',
            'min' => 'La quantité en stock doit être d\'au moins :min.',
            'regex' => 'La quantité en stock doit être un nombre entier positif (ne peut pas commencer par 0).',
        ],
        'sku' => [
            'required' => 'Le SKU est obligatoire.',
            'string' => 'Le SKU doit être une chaîne de caractères.',
            'unique' => 'Ce SKU est déjà utilisé par un autre produit.',
        ],
        'category_id' => [
            'required' => 'La catégorie est obligatoire.',
            'exists' => 'La catégorie sélectionnée n\'existe pas.',
        ],
        'images' => [
            'array' => 'Les images doivent être un tableau.',
            'max' => 'Vous ne pouvez pas télécharger plus de :max images.',
        ],
        'images.*' => [
            'required' => 'Chaque image est requise.',
            'image' => 'Le fichier doit être une image.',
            'mimes' => 'L\'image doit être de type : :values.',
            'max' => 'L\'image ne doit pas dépasser :max kilo-octets.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs Personnalisés
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'nom' => 'nom',
        'prenom' => 'prénom',
        'email' => 'adresse email',
        'numero_telephone' => 'numéro de téléphone',
        'numero_whatsapp' => 'numéro WhatsApp',
        'quartier' => 'quartier',
        'localisation' => 'localisation',
        'role' => 'rôle',
        'status' => 'statut',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'name' => 'nom du produit',
        'description' => 'description',
        'price' => 'prix',
        'cost_price' => 'prix de revient',
        'wholesale_price' => 'prix de gros',
        'retail_price' => 'prix de détail',
        'stock_quantity' => 'quantité en stock',
        'min_stock_alert' => 'alerte de stock minimum',
        'sku' => 'SKU',
        'barcode' => 'code-barres',
        'category_id' => 'catégorie',
        'images' => 'images',
        'status' => 'statut',
        'is_featured' => 'produit vedette',
        'meta_title' => 'titre meta',
        'meta_description' => 'description meta',
        'tags' => 'étiquettes',
    ],
];

