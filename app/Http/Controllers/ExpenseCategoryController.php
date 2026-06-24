<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:expense_categories,name',
        ]);

        $category = ExpenseCategory::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Expense category created successfully!',
            'category' => $category,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:expense_categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Expense category updated successfully!',
            'category' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Expense category deleted successfully!',
        ]);
    }
}
