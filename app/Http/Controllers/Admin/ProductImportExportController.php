<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSimple as Product;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// Utilisation des fonctions CSV natives de PHP

class ProductImportExportController extends Controller
{
    /**
     * Display the import/export interface.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $categories = Category::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();

        return view('admin.products.import-export', compact(
            'totalProducts',
            'activeProducts',
            'categories',
            'productTypes'
        ));
    }

    /**
     * Export products to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Product::with(['category', 'productType']);

        // Filtres d'export
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        $products = $query->get();

        // Créer le fichier CSV
        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_export');
        $file = fopen($tempFile, 'w');

        // En-têtes CSV
        fputcsv($file, [
            'ID', 'Nom', 'Description', 'Prix', 'Prix de revient', 'Prix de gros',
            'Prix de détail', 'Quantité minimum gros', 'Stock', 'Stock minimum',
            'Catégorie', 'Type de produit', 'SKU', 'Code-barres', 'Statut',
            'Vedette', 'Meta titre', 'Meta description', 'Tags', 'Images',
            'Date de création', 'Date de mise à jour'
        ]);

        // Données des produits
        foreach ($products as $product) {
            fputcsv($file, [
                $product->id,
                $product->name,
                $product->description,
                $product->price,
                $product->cost_price,
                $product->wholesale_price,
                $product->retail_price,
                $product->min_wholesale_quantity,
                $product->stock_quantity,
                $product->min_stock_alert,
                $product->category ? $product->category->name : '',
                $product->productType ? $product->productType->name : '',
                $product->sku,
                $product->barcode,
                $product->status,
                $product->is_featured ? 'Oui' : 'Non',
                $product->meta_title,
                $product->meta_description,
                is_array($product->tags) ? implode(', ', $product->tags) : $product->tags,
                is_array($product->images) ? implode(', ', $product->images) : $product->images,
                $product->created_at->format('Y-m-d H:i:s'),
                $product->updated_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($file);
        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import products from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = [];

            // Lire le fichier CSV
            if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
                $headers = fgetcsv($handle, 1000, ','); // Lire les en-têtes

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $csvData[] = array_combine($headers, $data);
                }
                fclose($handle);
            }

            $imported = 0;
            $errors = [];

            foreach ($csvData as $offset => $record) {
                try {
                    // Trouver la catégorie
                    $category = null;
                    if (!empty($record['Catégorie'])) {
                        $category = Category::where('name', $record['Catégorie'])->first();
                    }

                    // Trouver le type de produit
                    $productType = null;
                    if (!empty($record['Type de produit'])) {
                        $productType = ProductType::where('name', $record['Type de produit'])->first();
                    }

                    // Créer ou mettre à jour le produit
                    $productData = [
                        'name' => $record['Nom'] ?? '',
                        'description' => $record['Description'] ?? '',
                        'price' => floatval($record['Prix'] ?? 0),
                        'cost_price' => floatval($record['Prix de revient'] ?? 0),
                        'wholesale_price' => floatval($record['Prix de gros'] ?? 0),
                        'retail_price' => floatval($record['Prix de détail'] ?? 0),
                        'min_wholesale_quantity' => intval($record['Quantité minimum gros'] ?? 0),
                        'stock_quantity' => intval($record['Stock'] ?? 0),
                        'min_stock_alert' => intval($record['Stock minimum'] ?? 0),
                        'category_id' => $category ? $category->id : null,
                        'product_type_id' => $productType ? $productType->id : null,
                        'sku' => $record['SKU'] ?? '',
                        'barcode' => $record['Code-barres'] ?? '',
                        'status' => $record['Statut'] ?? 'active',
                        'is_featured' => ($record['Vedette'] ?? 'Non') === 'Oui',
                        'meta_title' => $record['Meta titre'] ?? '',
                        'meta_description' => $record['Meta description'] ?? '',
                        'tags' => !empty($record['Tags']) ? explode(', ', $record['Tags']) : [],
                        'images' => !empty($record['Images']) ? explode(', ', $record['Images']) : [],
                    ];

                    Product::updateOrCreate(
                        ['sku' => $productData['sku']],
                        $productData
                    );

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($offset + 1) . ": " . $e->getMessage();
                }
            }

            $message = "Import terminé. {$imported} produits importés avec succès.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . count($errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_produits.csv';
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_template');
        $file = fopen($tempFile, 'w');

        // En-têtes du modèle
        fputcsv($file, [
            'Nom', 'Description', 'Prix', 'Prix de revient', 'Prix de gros',
            'Prix de détail', 'Quantité minimum gros', 'Stock', 'Stock minimum',
            'Catégorie', 'Type de produit', 'SKU', 'Code-barres', 'Statut',
            'Vedette', 'Meta titre', 'Meta description', 'Tags', 'Images'
        ]);

        // Exemple de données
        fputcsv($file, [
            'Exemple Produit 1', 'Description du produit', '100.00', '50.00', '80.00',
            '120.00', '10', '100', '5', 'Électronique', 'Téléphone', 'SKU001',
            '1234567890123', 'active', 'Oui', 'Meta titre', 'Meta description',
            'tag1, tag2', 'image1.jpg, image2.jpg'
        ]);

        fclose($file);
        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Bulk update products.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_category',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'category_id' => 'required_if:action,change_category|exists:categories,id',
        ]);

        $productIds = $request->product_ids;
        $count = 0;

        switch ($request->action) {
            case 'activate':
                $count = Product::whereIn('id', $productIds)->update(['status' => 'active']);
                break;
            case 'deactivate':
                $count = Product::whereIn('id', $productIds)->update(['status' => 'inactive']);
                break;
            case 'delete':
                $count = Product::whereIn('id', $productIds)->delete();
                break;
            case 'change_category':
                $count = Product::whereIn('id', $productIds)->update(['category_id' => $request->category_id]);
                break;
        }

        return redirect()->back()->with('success', "{$count} produits mis à jour avec succès.");
    }

    /**
     * Export statistics.
     */
    public function exportStatistics()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'inactive_products' => Product::where('status', 'inactive')->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'products_by_category' => DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('COUNT(*) as count'))
                ->groupBy('categories.name')
                ->get(),
            'products_by_status' => Product::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
        ];

        $filename = 'products_statistics_' . date('Y-m-d_H-i-s') . '.json';

        return response(json_encode($stats, JSON_PRETTY_PRINT), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
