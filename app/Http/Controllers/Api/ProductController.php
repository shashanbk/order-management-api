<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }
        $query->latest('id'); 
        $products = $query->paginate(10);

        return ProductResource::collection($products)->additional([
            'message' => 'Products retrieved successfully',
            'status' => 'success'
        ]);
    }

    // 2. Create Product (Protected)
    public function store(StoreProductRequest $request) {
        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    // 3. Get Single Product (Public)
    public function show(Product $product) {
        $product = Product::find($product->id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return (new ProductResource($product))->additional([
            'message' => 'Product retrieved successfully',
            'status' => 'success'
        ]);
    }

    // 4. Update Product (Protected)
    public function update(Request $request, Product $product) {
        $data = $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'stock' => 'integer',
        ]);

        $product->update($data);
        return (new ProductResource($product))->additional([
            'message' => 'Product updated successfully',
            'status' => 'success'
        ]);
    }

    // 5. Delete Product (Protected)
    public function destroy($id) {
        // We use find() which returns null if not found (instead of crashing)
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
            'message' => 'Product not found or already deleted'
        ], 404);
    }

    $product->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Product deleted successfully'
    ]);
    }
}