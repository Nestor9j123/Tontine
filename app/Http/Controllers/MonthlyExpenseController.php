<?php

namespace App\Http\Controllers;

use App\Models\MonthlyExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:secretary|super_admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $now = now();
        $month = (int) $request->get('month', $now->month);
        $year = (int) $request->get('year', $now->year);

        // Charges du mois sélectionné
        $query = MonthlyExpense::forMonth($month, $year)
            ->with(['user', 'creator'])
            ->orderBy('expense_date', 'desc');

        $perPage = (int) $request->get('per_page', 15);
        $expenses = $query->paginate($perPage)->appends($request->except('page'));

        // Statistiques par type de charge
        $stats = [
            'total_month' => MonthlyExpense::forMonth($month, $year)->sum('amount'),
            'electricity' => MonthlyExpense::forMonth($month, $year)->byType('electricity')->sum('amount'),
            'rent' => MonthlyExpense::forMonth($month, $year)->byType('rent')->sum('amount'),
            'agent_expenses' => MonthlyExpense::forMonth($month, $year)->byType('agent_expense')->sum('amount'),
            'general' => MonthlyExpense::forMonth($month, $year)->byType('general')->sum('amount'),
        ];

        $agents = User::role('agent')->get();

        return view('expenses.index', compact('expenses', 'stats', 'agents', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agents = User::role('agent')->get();
        return view('expenses.create', compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:electricity,rent,agent_expense,general',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'user_id' => 'nullable|exists:users,id|required_if:type,agent_expense',
            'notes' => 'nullable|string',
        ]);

        $expenseDate = \Carbon\Carbon::parse($validated['date']);

        $data = [
            'description' => $validated['description'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'expense_date' => $expenseDate,
            'expense_month' => $expenseDate->month,
            'expense_year' => $expenseDate->year,
            'user_id' => $validated['type'] === 'agent_expense' ? ($validated['user_id'] ?? null) : null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        MonthlyExpense::create($data);

        return redirect()->route('expenses.index')
                        ->with('success', 'Charge enregistrée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyExpense $expense)
    {
        $expense->load(['user', 'creator']);
        
        // Si c'est une requête AJAX, retourner du JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $expense->id,
                'description' => $expense->description,
                'type' => $expense->type,
                'type_label' => $expense->type_human,
                'amount' => $expense->amount,
                'expense_date' => $expense->expense_date->toISOString(),
                'notes' => $expense->notes,
                'creator' => $expense->creator ? [
                    'id' => $expense->creator->id,
                    'name' => $expense->creator->name
                ] : null,
                'user' => $expense->user ? [
                    'id' => $expense->user->id,
                    'name' => $expense->user->name
                ] : null,
                'created_at' => $expense->created_at->toISOString(),
            ]);
        }
        
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyExpense $expense)
    {
        $agents = User::role('agent')->get();
        return view('expenses.edit', compact('expense', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyExpense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:electricity,rent,agent_expense,general',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'user_id' => 'nullable|exists:users,id|required_if:type,agent_expense',
            'notes' => 'nullable|string',
        ]);

        $expenseDate = \Carbon\Carbon::parse($validated['date']);
        
        $data = [
            'description' => $validated['description'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'expense_date' => $expenseDate,
            'expense_month' => $expenseDate->month,
            'expense_year' => $expenseDate->year,
            'user_id' => $validated['type'] === 'agent_expense' ? ($validated['user_id'] ?? null) : null,
            'notes' => $validated['notes'] ?? null,
        ];

        $expense->update($data);

        return redirect()->route('expenses.index')
                        ->with('success', 'Charge mensuelle mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyExpense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
                        ->with('success', 'Charge mensuelle supprimée avec succès.');
    }

    /**
     * Rapport mensuel des charges
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $expenses = MonthlyExpense::forMonth($month, $year)
                                ->with(['user', 'creator'])
                                ->orderBy('expense_date')
                                ->get();

        $groupedExpenses = $expenses->groupBy('type');
        $totalByType = $expenses->groupBy('type')->map->sum('amount');
        $totalMonth = $expenses->sum('amount');

        $agentExpenses = $expenses->where('type', 'agent_expense')
                               ->groupBy('user_id')
                               ->map(function($expenses) {
                                   return [
                                       'agent' => $expenses->first()->user,
                                       'total' => $expenses->sum('amount'),
                                       'expenses' => $expenses
                                   ];
                               });

        return view('expenses.monthly-report', compact(
            'expenses', 'groupedExpenses', 'totalByType', 'totalMonth', 
            'agentExpenses', 'month', 'year'
        ));
    }
}
