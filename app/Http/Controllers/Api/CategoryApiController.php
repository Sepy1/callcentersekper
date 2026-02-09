<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller
{
    /**
     * Return JSON list of categories.
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get(['id','name','description']);
        return response()->json(['data' => $categories], 200);
    }
}
