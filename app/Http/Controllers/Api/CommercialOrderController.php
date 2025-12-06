<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommercialOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'commercial') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $orders = Order::whereHas('user', function($query) use ($user) {
            $query->where('commercial_id', $user->id);
        })->with(['user', 'orderItems.product'])->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);

        if ($user->role !== 'admin' && 
            ($user->role !== 'commercial' || $order->user->commercial_id !== $user->id)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return response()->json($order);
    }
}