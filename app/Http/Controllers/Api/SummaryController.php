<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Carbon\Carbon;

class SummaryController extends Controller
{
    /**
     * Retorna o resumo financeiro do usuário autenticado.
     *
     * Permite filtrar por mês e ano (ex: /api/summary?month=10&year=2025)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pega o mês e ano atuais, se não forem passados na query
        $month = $request->query('month', Carbon::now()->month);
        $year  = $request->query('year', Carbon::now()->year);

        // Busca todas as transações do usuário no mês/ano especificado
        $transactions = Transaction::where('user_id', $user->id)
            ->whereYear('happened_at', $year)
            ->whereMonth('happened_at', $month)
            ->get();

        // Calcula totais
        $totalIncomes = $transactions->where('direction', 'income')->sum('amount');
        $totalExpenses = $transactions->where('direction', 'expense')->sum('amount');
        $balance = $totalIncomes - $totalExpenses;

        // Agrupa por categoria (para gráficos)
        $byCategory = $transactions
            ->groupBy('category_id')
            ->map(function ($group) {
                return [
                    'category_name' => optional($group->first()->category)->name,
                    'type' => optional($group->first()->category)->type,
                    'total' => $group->sum('amount'),
                ];
            })
            ->values();

        // Retorna o resumo formatado
        return response()->json([
            'month' => (int) $month,
            'year' => (int) $year,
            'totals' => [
                'income' => $totalIncomes,
                'expense' => $totalExpenses,
                'balance' => $balance,
            ],
            'categories' => $byCategory,
            'transactions_count' => $transactions->count(),
            'transactions' => $transactions->load('category'),
        ]);
    }
}
