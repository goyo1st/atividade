<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mês e ano atuais (ou selecionados)
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Busca todas as transações do usuário logado, no mês e ano
        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('happened_at', $month)
            ->whereYear('happened_at', $year)
            ->with('category')
            ->orderBy('happened_at', 'desc')
            ->get();

        // Calcula totais
        $totalIncome = $transactions->where('direction', 'income')->sum('amount');
        $totalExpense = $transactions->where('direction', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('dashboard', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'month',
            'year'
        ));

        
    }

    public function create()
    {
         $categories = \App\Models\Category::all(); // Busca todas as categorias
    return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'direction' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'happened_at' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()->route('dashboard')->with('success', 'Transação adicionada com sucesso!');
    }
}
