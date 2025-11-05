<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Recherche de produits (autocomplete)
     */
    public function products(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            // Améliorer la recherche avec priorité : nom exact > nom commence par > nom contient > description
            $searchTerm = trim($query);
            $products = DB::select("
                SELECT 
                    p.id, 
                    p.name, 
                    p.sku, 
                    p.price, 
                    p.stock_quantity,
                    p.description,
                    c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE (
                    p.name = ? OR
                    p.name LIKE ? OR 
                    p.name LIKE ? OR 
                    p.sku LIKE ? OR 
                    p.description LIKE ?
                )
                AND p.status = 'active'
                AND p.deleted_at IS NULL
                ORDER BY 
                    CASE 
                        WHEN p.name = ? THEN 1
                        WHEN p.name LIKE ? THEN 2
                        WHEN p.name LIKE ? THEN 3
                        WHEN p.sku LIKE ? THEN 4
                        ELSE 5
                    END,
                    p.name ASC
                LIMIT ?
            ", [
                $searchTerm,                    // WHERE nom exact
                $searchTerm . '%',              // WHERE nom commence par
                '%' . $searchTerm . '%',       // WHERE nom contient
                '%' . $searchTerm . '%',        // WHERE SKU contient
                '%' . $searchTerm . '%',        // WHERE description contient
                $searchTerm,                    // ORDER BY nom exact
                $searchTerm . '%',              // ORDER BY nom commence par
                '%' . $searchTerm . '%',       // ORDER BY nom contient
                '%' . $searchTerm . '%',       // ORDER BY SKU contient
                $limit
            ]);

            $results = array_map(function($product) use ($searchTerm) {
                // Formater le prix de manière plus lisible
                $priceFormatted = $product->price > 0 
                    ? number_format($product->price, 0, ',', ' ') . ' FCFA' 
                    : 'Prix non défini';
                
                // Informations de stock
                $stockInfo = $product->stock_quantity > 0 
                    ? 'En stock (' . $product->stock_quantity . ')' 
                    : 'Rupture de stock';
                
                // Construire le sous-titre de manière plus claire
                $subtitleParts = [];
                if (!empty($product->category_name)) {
                    $subtitleParts[] = $product->category_name;
                }
                $subtitleParts[] = $priceFormatted;
                $subtitleParts[] = $stockInfo;
                
                $subtitle = implode(' • ', $subtitleParts);
                
                return [
                    'id' => $product->id,
                    'title' => $product->name,
                    'subtitle' => $subtitle,
                    'value' => $product->name,
                    'url' => route('admin.products.show', $product->id),
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'stock' => $product->stock_quantity
                ];
            }, $products);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche produits: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche d'utilisateurs (autocomplete)
     */
    public function users(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            $users = DB::select("
                SELECT id, nom, prenom, email, numero_telephone, role, status
                FROM users 
                WHERE (
                    nom LIKE ? OR nom LIKE ? OR
                    prenom LIKE ? OR prenom LIKE ? OR
                    email LIKE ? OR email LIKE ? OR
                    numero_telephone LIKE ?
                )
                AND deleted_at IS NULL
                ORDER BY 
                    CASE 
                        WHEN CONCAT(nom, ' ', prenom) = ? THEN 1
                        WHEN CONCAT(nom, ' ', prenom) LIKE ? THEN 2
                        WHEN nom LIKE ? OR prenom LIKE ? THEN 3
                        WHEN email LIKE ? THEN 4
                        ELSE 5
                    END,
                    nom ASC, prenom ASC
                LIMIT ?
            ", [
                $searchTerm . '%',           // Nom commence par
                '%' . $searchTerm . '%',     // Nom contient
                $searchTerm . '%',           // Prénom commence par
                '%' . $searchTerm . '%',     // Prénom contient
                $searchTerm . '%',           // Email commence par
                '%' . $searchTerm . '%',     // Email contient
                '%' . $searchTerm . '%',     // Téléphone contient
                $searchTerm,                 // ORDER BY exact
                $searchTerm . '%',           // ORDER BY commence par
                $searchTerm . '%',           // ORDER BY nom/prénom commence par
                $searchTerm . '%',           // ORDER BY nom/prénom commence par
                $searchTerm . '%',           // ORDER BY email commence par
                $limit
            ]);

            $results = array_map(function($user) {
                $fullName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? ''));
                $roleLabel = ucfirst($user->role ?? 'client');
                $statusLabel = match($user->status) {
                    'active' => 'Actif',
                    'pending' => 'En attente',
                    'inactive' => 'Inactif',
                    'suspended' => 'Suspendu',
                    default => ucfirst($user->status ?? '')
                };
                
                $subtitleParts = [];
                if (!empty($user->email)) {
                    $subtitleParts[] = $user->email;
                }
                if (!empty($user->numero_telephone)) {
                    $subtitleParts[] = $user->numero_telephone;
                }
                $subtitleParts[] = $roleLabel;
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $user->id,
                    'title' => $fullName ?: 'Utilisateur #' . $user->id,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $fullName ?: $user->email,
                    'url' => route('admin.users.show', $user->id),
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status
                ];
            }, $users);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche utilisateurs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche de commandes (autocomplete)
     */
    public function orders(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            $isNumeric = is_numeric($searchTerm);
            
            // Construire la requête SQL et les paramètres de manière cohérente
            $params = [];
            
            // Construire la clause WHERE
            $whereConditions = [];
            if ($isNumeric) {
                $whereConditions[] = "o.id = ?";
                $params[] = $searchTerm;
            }
            $whereConditions[] = "o.order_number LIKE ?";
            $params[] = $searchTerm;
            $whereConditions[] = "o.order_number LIKE ?";
            $params[] = $searchTerm . '%';
            $whereConditions[] = "u.nom LIKE ?";
            $params[] = '%' . $searchTerm . '%';
            $whereConditions[] = "u.prenom LIKE ?";
            $params[] = '%' . $searchTerm . '%';
            $whereConditions[] = "u.email LIKE ?";
            $params[] = '%' . $searchTerm . '%';
            $whereConditions[] = "CONCAT(COALESCE(u.nom, ''), ' ', COALESCE(u.prenom, '')) LIKE ?";
            $params[] = '%' . $searchTerm . '%';
            
            // Construire la clause ORDER BY CASE
            $orderByCase = [];
            if ($isNumeric) {
                $orderByCase[] = "WHEN o.id = ? THEN 1";
                $params[] = $searchTerm;
            }
            $orderByCase[] = "WHEN o.order_number = ? THEN " . ($isNumeric ? "2" : "1");
            $params[] = $searchTerm;
            $orderByCase[] = "WHEN o.order_number LIKE ? THEN " . ($isNumeric ? "3" : "2");
            $params[] = $searchTerm . '%';
            $orderByCase[] = "WHEN CONCAT(COALESCE(u.nom, ''), ' ', COALESCE(u.prenom, '')) LIKE ? THEN " . ($isNumeric ? "4" : "3");
            $params[] = $searchTerm . '%';
            $orderByCase[] = "WHEN u.nom LIKE ? OR u.prenom LIKE ? THEN " . ($isNumeric ? "5" : "4");
            $params[] = $searchTerm . '%';
            $params[] = $searchTerm . '%';
            
            // Construire la requête SQL complète
            $sql = "
                SELECT 
                    o.id, 
                    o.order_number, 
                    o.total_amount, 
                    o.status, 
                    o.created_at,
                    u.nom, 
                    u.prenom, 
                    u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE (" . implode(' OR ', $whereConditions) . ")
                AND o.deleted_at IS NULL
                ORDER BY 
                    CASE 
                        " . implode("\n                        ", $orderByCase) . "
                        ELSE " . ($isNumeric ? "6" : "5") . "
                    END,
                    o.created_at DESC
                LIMIT ?
            ";
            
            $params[] = $limit;
            $orders = DB::select($sql, $params);

            $results = array_map(function($order) {
                $clientName = trim(($order->nom ?? '') . ' ' . ($order->prenom ?? ''));
                $orderLabel = $order->order_number ?? '#' . $order->id;
                
                // Formater le statut
                $statusLabel = match($order->status) {
                    'pending' => 'En attente',
                    'confirmed' => 'Confirmée',
                    'processing' => 'En traitement',
                    'shipped' => 'Expédiée',
                    'delivered' => 'Livrée',
                    'cancelled' => 'Annulée',
                    'completed' => 'Terminée',
                    default => ucfirst($order->status ?? '')
                };
                
                $subtitleParts = [];
                if ($clientName) {
                    $subtitleParts[] = $clientName;
                }
                $subtitleParts[] = number_format($order->total_amount, 0, ',', ' ') . ' FCFA';
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $order->id,
                    'title' => 'Commande ' . $orderLabel,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $orderLabel,
                    'url' => route('admin.orders.show', $order->id),
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total' => $order->total_amount
                ];
            }, $orders);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche commandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche de clients (autocomplete)
     */
    public function clients(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            $clients = DB::select("
                SELECT id, nom, prenom, email, numero_telephone, quartier, status
                FROM users 
                WHERE role = 'client'
                AND (
                    nom LIKE ? OR nom LIKE ? OR
                    prenom LIKE ? OR prenom LIKE ? OR
                    email LIKE ? OR email LIKE ? OR
                    numero_telephone LIKE ? OR
                    quartier LIKE ?
                )
                AND deleted_at IS NULL
                ORDER BY 
                    CASE 
                        WHEN CONCAT(nom, ' ', prenom) = ? THEN 1
                        WHEN CONCAT(nom, ' ', prenom) LIKE ? THEN 2
                        WHEN nom LIKE ? OR prenom LIKE ? THEN 3
                        WHEN email LIKE ? THEN 4
                        ELSE 5
                    END,
                    nom ASC, prenom ASC
                LIMIT ?
            ", [
                $searchTerm . '%',           // Nom commence par
                '%' . $searchTerm . '%',     // Nom contient
                $searchTerm . '%',           // Prénom commence par
                '%' . $searchTerm . '%',     // Prénom contient
                $searchTerm . '%',           // Email commence par
                '%' . $searchTerm . '%',     // Email contient
                '%' . $searchTerm . '%',     // Téléphone contient
                '%' . $searchTerm . '%',     // Quartier contient
                $searchTerm,                 // ORDER BY exact
                $searchTerm . '%',           // ORDER BY commence par
                $searchTerm . '%',           // ORDER BY nom/prénom commence par
                $searchTerm . '%',           // ORDER BY nom/prénom commence par
                $searchTerm . '%',           // ORDER BY email commence par
                $limit
            ]);

            $results = array_map(function($client) {
                $fullName = trim(($client->nom ?? '') . ' ' . ($client->prenom ?? ''));
                $statusLabel = match($client->status) {
                    'active' => 'Actif',
                    'pending' => 'En attente',
                    'inactive' => 'Inactif',
                    'suspended' => 'Suspendu',
                    default => ucfirst($client->status ?? '')
                };
                
                $subtitleParts = [];
                if (!empty($client->email)) {
                    $subtitleParts[] = $client->email;
                }
                if (!empty($client->numero_telephone)) {
                    $subtitleParts[] = $client->numero_telephone;
                }
                if (!empty($client->quartier)) {
                    $subtitleParts[] = $client->quartier;
                }
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $client->id,
                    'title' => $fullName ?: 'Client #' . $client->id,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $fullName ?: $client->email,
                    'url' => route('admin.clients.show', $client->id),
                    'email' => $client->email,
                    'status' => $client->status,
                    'quartier' => $client->quartier
                ];
            }, $clients);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche clients: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche de catégories (autocomplete)
     */
    public function categories(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            $categories = DB::select("
                SELECT id, name, description, is_active, slug
                FROM categories 
                WHERE (
                    name LIKE ? OR name LIKE ? OR
                    description LIKE ? OR
                    slug LIKE ?
                )
                AND deleted_at IS NULL
                ORDER BY 
                    CASE 
                        WHEN name = ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        WHEN slug LIKE ? THEN 4
                        ELSE 5
                    END,
                    name ASC
                LIMIT ?
            ", [
                $searchTerm,                    // Nom exact
                $searchTerm . '%',              // Nom commence par
                '%' . $searchTerm . '%',       // Description contient
                '%' . $searchTerm . '%',       // Slug contient
                $searchTerm,                    // ORDER BY exact
                $searchTerm . '%',              // ORDER BY commence par
                '%' . $searchTerm . '%',       // ORDER BY contient
                '%' . $searchTerm . '%',       // ORDER BY slug
                $limit
            ]);

            $results = array_map(function($category) {
                $statusLabel = $category->is_active ? 'Actif' : 'Inactif';
                
                $subtitleParts = [];
                if (!empty($category->description)) {
                    $subtitleParts[] = mb_substr($category->description, 0, 50) . (mb_strlen($category->description) > 50 ? '...' : '');
                }
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $category->id,
                    'title' => $category->name,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $category->name,
                    'url' => route('admin.categories.show', $category->id),
                    'is_active' => $category->is_active
                ];
            }, $categories);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche catégories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche d'attributs (autocomplete)
     */
    public function attributes(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            // Recherche avec tri par pertinence (identique aux produits)
            // Note: La table attributes n'utilise pas soft deletes (pas de deleted_at)
            $attributes = DB::select("
                SELECT id, name, slug, is_active
                FROM attributes 
                WHERE (
                    name = ? OR
                    name LIKE ? OR 
                    name LIKE ? OR
                    slug LIKE ?
                )
                ORDER BY 
                    CASE 
                        WHEN name = ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        WHEN slug LIKE ? THEN 4
                        ELSE 5
                    END,
                    name ASC
                LIMIT ?
            ", [
                $searchTerm,                    // WHERE nom exact
                $searchTerm . '%',              // WHERE nom commence par
                '%' . $searchTerm . '%',       // WHERE nom contient
                '%' . $searchTerm . '%',       // WHERE slug contient
                $searchTerm,                    // ORDER BY nom exact
                $searchTerm . '%',              // ORDER BY nom commence par
                '%' . $searchTerm . '%',       // ORDER BY nom contient
                '%' . $searchTerm . '%',       // ORDER BY slug contient
                $limit
            ]);

            $results = array_map(function($attribute) {
                // Utiliser is_active au lieu de status
                $statusLabel = isset($attribute->is_active) && $attribute->is_active ? 'Actif' : 'Inactif';
                
                $subtitleParts = [];
                if (!empty($attribute->slug)) {
                    $subtitleParts[] = 'Slug: ' . $attribute->slug;
                }
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $attribute->id,
                    'title' => $attribute->name,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $attribute->name,
                    'url' => route('admin.attributes.show', $attribute->id),
                    'is_active' => $attribute->is_active ?? true
                ];
            }, $attributes);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche attributs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Recherche de types de produits (autocomplete)
     */
    public function productTypes(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 10), 20);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $searchTerm = trim($query);
            $productTypes = DB::select("
                SELECT id, name, description, is_active
                FROM product_types 
                WHERE (
                    name LIKE ? OR name LIKE ? OR
                    description LIKE ?
                )
                AND deleted_at IS NULL
                ORDER BY 
                    CASE 
                        WHEN name = ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        ELSE 4
                    END,
                    name ASC
                LIMIT ?
            ", [
                $searchTerm,                    // Nom exact
                $searchTerm . '%',              // Nom commence par
                '%' . $searchTerm . '%',       // Description contient
                $searchTerm,                    // ORDER BY exact
                $searchTerm . '%',              // ORDER BY commence par
                '%' . $searchTerm . '%',       // ORDER BY contient
                $limit
            ]);

            $results = array_map(function($productType) {
                $statusLabel = $productType->is_active ? 'Actif' : 'Inactif';
                
                $subtitleParts = [];
                if (!empty($productType->description)) {
                    $subtitleParts[] = mb_substr($productType->description, 0, 50) . (mb_strlen($productType->description) > 50 ? '...' : '');
                }
                $subtitleParts[] = $statusLabel;
                
                return [
                    'id' => $productType->id,
                    'title' => $productType->name,
                    'subtitle' => implode(' • ', $subtitleParts),
                    'value' => $productType->name,
                    'url' => route('admin.product-types.show', $productType->id),
                    'is_active' => $productType->is_active
                ];
            }, $productTypes);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche types de produits: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }
}

