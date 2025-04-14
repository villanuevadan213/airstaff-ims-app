<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function dashboard()
    {
        $totalProducts = Product::count();

        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')->simplePaginate(10);

        $recentMovements = StockMovement::latest()->take(5)->get();

        return view('dashboard', compact('totalProducts', 'lowStock', 'recentMovements'));
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $products = Product::with('category')
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'like', '%' . $searchTerm . '%')
                             ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->simplePaginate(10);

        return view('inventory.index', compact('products', 'searchTerm'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('inventory.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'quantity' => 'required|integer',
            'unit' => 'required|string',
            'reorder_level' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'reorder_level' => $request->reorder_level,
            'category_id' => $request->category_id,
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $product->quantity,
            'reference' => 'Initial stock setup',
            'price' => 0,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product added successfully');
    }


    public function show($id)
    {
        $product = Product::with('category', 'stockMovements')->findOrFail($id);
        return view('inventory.show', compact('product'));
    }


    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('inventory.edit', compact('product', 'categories'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'quantity' => 'required|integer',
            'unit' => 'required|string',
            'reorder_level' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::findOrFail($id);
        $originalQuantity = $product->quantity;

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'reorder_level' => $request->reorder_level,
            'category_id' => $request->category_id,
        ]);

        if ($product->quantity != $originalQuantity) {
            $movementType = $product->quantity < $originalQuantity ? 'out' : 'in';
            StockMovement::create([
                'product_id' => $product->id,
                'type' => $movementType,
                'quantity' => abs($product->quantity - $originalQuantity),
                'reference' => 'Stock updated',
                'price' => 0,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully');
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $product->quantity,
            'reference' => 'Product deleted',
            'price' => 0,
        ]);

        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully');
    }
}
