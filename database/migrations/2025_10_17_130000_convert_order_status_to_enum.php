<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier que les statuts existants sont valides
        $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'completed'];

        // Récupérer tous les statuts uniques dans la table orders
        $existingStatuses = DB::table('orders')
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->toArray();

        echo "Statuts existants trouvés: " . implode(', ', $existingStatuses) . "\n";

        // Vérifier s'il y a des statuts invalides
        $invalidStatuses = array_diff($existingStatuses, $validStatuses);

        if (!empty($invalidStatuses)) {
            echo "Statuts invalides trouvés: " . implode(', ', $invalidStatuses) . "\n";
            echo "Conversion vers 'pending'...\n";

            // Convertir les statuts invalides vers 'pending'
            foreach ($invalidStatuses as $invalidStatus) {
                DB::table('orders')
                    ->where('status', $invalidStatus)
                    ->update(['status' => 'pending']);
            }
        }

        echo "Conversion des statuts terminée.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire car on ne modifie pas la structure
        echo "Rollback: Aucune action nécessaire.\n";
    }
};
