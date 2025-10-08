<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    // Liste des quartiers disponibles (statique car pas de table séparée)
    public static $quartiers = [
        'Bilbalogo',
        'Saint Léon',
        'Zangouettin',
        'Tiedpalogo',
        'Koulouba',
        'Kamsonghin',
        'Samandin',
        'Gounghin Sud',
        'Gandin',
        'Kouritenga',
        'Mankoudougou',
        'Paspanga',
        'Ouidi',
        'Larlé',
        'Kologh Naba',
        'Dapoya II',
        'Nemnin',
        'Niogsin',
        'Hamdalaye',
        'Gounghin Nord',
        'Baoghin',
        'Camp militaire',
        'Naab Pougo',
        'Kienbaoghin',
        'Zongo',
        'Koumdayonré',
        'Nonsin',
        'Rimkiéta',
        'Tampouy',
        'Kilwin',
        'Tanghin',
        'Sambin Barrage',
        'Somgandé',
        'Zone industrielle',
        'Nioko II',
        'Bendogo',
        'Toukin',
        'Zogona',
        'Wemtenga',
        'Dagnoën',
        'Ronsin',
        'Kalgondin',
        'Cissin',
        'Pissy',
        'Nagrin',
        'Yaoghin',
        'Sandogo',
        'Kankamsin',
        'Boassa',
        'Zagtouli Nord',
        'Zagtouli Sud',
        'Zongo Nabitenga',
        'Sogpèlcé',
        'Bissighin',
        'Bassinko',
        'Dar-es-Salaam',
        'Silmiougou',
        'Gantin',
        'Bangpooré',
        'Larlé Wéogo',
        'Marcoussis',
        'Silmiyiri',
        'Wobriguéré',
        'Ouapassi',
        'Kossodo',
        'Wayalghin',
        'Godin',
        'Nioko I',
        'Dassasgho',
        'Taabtenga',
        'Karpala',
        'Balkuy',
        'Lanoayiri',
        'Dayongo',
        'Ouidtenga',
        'Patte d\'Oie',
        'Ouaga 2000',
        'Trame d\'accueil de Ouaga 2000'
    ];

    // Pas de table, donc pas de fillable
    protected $fillable = [];

    // Relations basées sur le champ quartier en string
    public static function getUsersByQuartier($quartier)
    {
        return User::where('quartier', $quartier);
    }

    public static function getClientsByQuartier($quartier)
    {
        return User::where('quartier', $quartier)->where('role', 'client');
    }

    public static function getActiveClientsByQuartier($quartier)
    {
        return User::where('quartier', $quartier)
            ->where('role', 'client')
            ->where('status', 'active');
    }

    // Méthodes statiques pour gérer les quartiers
    public static function getAllQuartiers()
    {
        $allQuartiers = [];
        foreach (self::$quartiers as $quartier) {
            $allQuartiers[] = [
                'nom' => $quartier,
                'full_name' => $quartier
            ];
        }
        return $allQuartiers;
    }

    public static function getQuartiers()
    {
        return self::$quartiers;
    }

    public static function getClientCountByQuartier($quartier)
    {
        return self::getClientsByQuartier($quartier)->count();
    }

    public static function getActiveClientCountByQuartier($quartier)
    {
        return self::getActiveClientsByQuartier($quartier)->count();
    }

    public static function getQuartierStats()
    {
        $stats = [];
        foreach (self::$quartiers as $quartier) {
            $stats[] = [
                'quartier' => $quartier,
                'total_clients' => self::getClientCountByQuartier($quartier),
                'active_clients' => self::getActiveClientCountByQuartier($quartier),
                'full_name' => $quartier
            ];
        }
        return $stats;
    }
}
