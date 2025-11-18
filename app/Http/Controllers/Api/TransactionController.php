<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Listar todas as transações do usuário autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('happened_at', 'desc')
            ->get();

        return response()->json($transactions);
    }
/**
 * Filtrar transações por mês e ano com resumo.
 */
public function filter(Request $request)
{
    $user = Auth::user();

    $month = $request->input('month');
    $year = $request->input('year');

    // Validação simples
    if (!$month || !$year) {
        return response()->json(['message' => 'Informe o mês e o ano.'], 400);
    }

    // Busca transações do usuário no mês/ano
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

    return response()->json([
        'transactions' => $transactions,
        'summary' => [
            'month' => $month,
            'year' => $year,
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $balance,
        ],
    ]);
}

    /**
     * Criar uma nova transação.
     */
    public function store(Request $request)
    {
        // Validação dos campos recebidos
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'direction'     => 'required|in:income,expense',
            'amount'        => 'required|numeric|min:0.01',
            'description'   => 'nullable|string|max:255',
            'happened_at'   => 'required|date',
            'recurrence_id' => 'nullable|exists:recurrences,id',
            'is_recurring'  => 'boolean',
        ]);

        // Criação da transação vinculada ao usuário logado
        $transaction = Transaction::create([
            'user_id'       => Auth::id(),
            'category_id'   => $validated['category_id'],
            'recurrence_id' => $validated['recurrence_id'] ?? null,
            'direction'     => $validated['direction'],
            'amount'        => $validated['amount'],
            'description'   => $validated['description'] ?? null,
            'happened_at'   => $validated['happened_at'],
            'is_recurring'  => $validated['is_recurring'] ?? false,
        ]);

        // Retorna a transação criada com status 201
        return response()->json([
            'message' => 'Transação criada com sucesso!',
            'data' => $transaction->load('category'),
        ], 201);
    }

    /**
     * Exibir uma transação específica.
     */
    public function show(string $id)
    {
        $transaction = Transaction::with('category')->findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Atualizar uma transação.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'category_id'   => 'sometimes|exists:categories,id',
            'direction'     => 'sometimes|in:income,expense',
            'amount'        => 'sometimes|numeric|min:0.01',
            'description'   => 'nullable|string|max:255',
            'happened_at'   => 'sometimes|date',
            'recurrence_id' => 'nullable|exists:recurrences,id',
            'is_recurring'  => 'boolean',
        ]);

        $transaction->update($validated);

        return response()->json([
            'message' => 'Transação atualizada com sucesso!',
            'data' => $transaction->load('category'),
        ]);
    }

    /**
     * Deletar uma transação.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'message' => 'Transação excluída com sucesso!',
        ]);
    }
}
