<?php

namespace App\Helpers;

use App\Models\Product;

class ProductTypeHelper
{
    /**
     * Détermine si un produit est un téléphone
     * 
     * @param Product|array $product
     * @return bool
     */
    public static function isTelephone($product): bool
    {
        // Si c'est un modèle Product
        if ($product instanceof Product) {
            $brand = $product->brand;
            $range = $product->range;
            $format = $product->format;
            $productType = $product->productType;
        } else {
            // Si c'est un tableau (depuis DB::table ou array)
            $brand = $product['brand'] ?? $product->brand ?? null;
            $range = $product['range'] ?? $product->range ?? null;
            $format = $product['format'] ?? $product->format ?? null;
            $productTypeName = $product['product_type_name'] ?? $product->product_type_name ?? null;
        }

        // Un produit est un téléphone s'il a brand, range ou format
        $hasPhoneFields = (!empty($brand) || !empty($range) || !empty($format));

        // Ou si le productType contient "téléphone"/"smartphone"
        $isPhoneType = false;
        if (isset($productType)) {
            $isPhoneType = $productType && (
                stripos($productType->name, 'téléphone') !== false ||
                stripos($productType->name, 'telephone') !== false ||
                stripos($productType->name, 'smartphone') !== false
            );
        } elseif (isset($productTypeName)) {
            $isPhoneType = $productTypeName && (
                stripos($productTypeName, 'téléphone') !== false ||
                stripos($productTypeName, 'telephone') !== false ||
                stripos($productTypeName, 'smartphone') !== false
            );
        }

        return $hasPhoneFields || $isPhoneType;
    }

    /**
     * Détermine si un produit est un accessoire
     * 
     * @param Product|array $product
     * @return bool
     */
    public static function isAccessoire($product): bool
    {
        // Si c'est un modèle Product
        if ($product instanceof Product) {
            $typeAccessory = $product->type_accessory;
            $compatibility = $product->compatibility;
            $productType = $product->productType;
        } else {
            // Si c'est un tableau (depuis DB::table ou array)
            $typeAccessory = $product['type_accessory'] ?? $product->type_accessory ?? null;
            $compatibility = $product['compatibility'] ?? $product->compatibility ?? null;
            $productTypeName = $product['product_type_name'] ?? $product->product_type_name ?? null;
        }

        // Un produit est un accessoire s'il a type_accessory ou compatibility
        $hasAccessoryFields = (!empty($typeAccessory) || !empty($compatibility));

        // Ou si le productType contient "accessoire"
        $isAccessoryType = false;
        if (isset($productType)) {
            $isAccessoryType = $productType && (
                stripos($productType->name, 'accessoire') !== false
            );
        } elseif (isset($productTypeName)) {
            $isAccessoryType = $productTypeName && (
                stripos($productTypeName, 'accessoire') !== false
            );
        }

        return $hasAccessoryFields || $isAccessoryType;
    }

    /**
     * Détermine le type de produit (telephone, accessoire, ou autre)
     * 
     * @param Product|array $product
     * @return string 'telephone', 'accessoire', ou 'other'
     */
    public static function getProductType($product): string
    {
        if (self::isTelephone($product)) {
            return 'telephone';
        } elseif (self::isAccessoire($product)) {
            return 'accessoire';
        }
        return 'other';
    }

    /**
     * Détermine si une commande contient des téléphones
     * 
     * @param \App\Models\Order $order
     * @return bool
     */
    public static function orderHasTelephones($order): bool
    {
        foreach ($order->items as $item) {
            if ($item->product && self::isTelephone($item->product)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Détermine si une commande contient des accessoires
     * 
     * @param \App\Models\Order $order
     * @return bool
     */
    public static function orderHasAccessoires($order): bool
    {
        foreach ($order->items as $item) {
            if ($item->product && self::isAccessoire($item->product)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Détermine le type principal d'une commande
     * 
     * @param \App\Models\Order $order
     * @return string 'telephone', 'accessoire', 'mixed', ou 'other'
     */
    public static function getOrderType($order): string
    {
        $hasTelephones = self::orderHasTelephones($order);
        $hasAccessoires = self::orderHasAccessoires($order);

        if ($hasTelephones && $hasAccessoires) {
            return 'mixed';
        } elseif ($hasTelephones) {
            return 'telephone';
        } elseif ($hasAccessoires) {
            return 'accessoire';
        }
        return 'other';
    }
}







