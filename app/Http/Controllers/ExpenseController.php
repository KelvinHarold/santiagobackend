<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
{
    $expenses = Expense::all();
    $total = $expenses->sum('amount');

    return response()->json([
        'expenses' => $expenses,
        'total_expenses' => $total,
    ]);
}


    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return response()->json(['message' => 'Expense deleted']);
    }
}
