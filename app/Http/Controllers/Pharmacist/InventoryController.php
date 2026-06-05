<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $filter = $request->filter; // 'low' | 'critical' | null

        $base = Medicine::query();
        $medicines = (clone $base)
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%"))
            ->when($filter === 'low', fn($query) => $query->whereBetween('stock', [11, 50]))
            ->when($filter === 'critical', fn($query) => $query->where('stock', '<=', 10))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'total' => (clone $base)->count(),
            'low' => (clone $base)->whereBetween('stock', [11, 50])->count(),
            'critical' => (clone $base)->where('stock', '<=', 10)->count(),
        ];

        return view('pharmacist.inventory.index', compact('medicines', 'q', 'filter', 'counts'));
    }

    public function updateStock(Request $request, Medicine $medicine)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $medicine->update(['stock' => $request->stock]);
        return back()->with('success', 'Stock updated.');
    }
}
