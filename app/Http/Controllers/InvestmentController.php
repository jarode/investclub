<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Notifications\NewInvestmentNotification;

class InvestmentController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Wyświetla listę inwestycji użytkownika.
     */
    public function index(Request $request)
    {
        // Pobieramy inwestycje bieżącego użytkownika
        $query = Investment::query();
        
        // Sprawdzamy czy użytkownik jest adminem lub managerem - wtedy może widzieć wszystkie inwestycje
        if (Auth::user()->hasAnyRole(['admin', 'manager'])) {
            // Dla managera filtrujemy tylko inwestycje w jego projektach
            if (Auth::user()->hasRole('manager') && !Auth::user()->isAdmin()) {
                $ownedProjectIds = Auth::user()->ownedProjects()->pluck('id');
                $query->whereIn('project_id', $ownedProjectIds);
            }
        } else {
            // Dla zwykłego użytkownika pokazujemy tylko jego inwestycje
            $query->where('user_id', Auth::id());
        }
        
        // Filtrowanie według statusu
        if ($request->has('status') && in_array($request->status, ['declared', 'paid', 'confirmed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        
        // Filtrowanie według projektu
        if ($request->has('project_id') && is_numeric($request->project_id)) {
            $query->where('project_id', $request->project_id);
        }
        
        // Sortowanie
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $allowedSortFields = ['amount', 'status', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }
        
        // Pobierz inwestycje z relacjami
        $investments = $query->with(['project', 'user'])->paginate(10);
        
        return view('investments.index', compact('investments'));
    }

    /**
     * Wyświetla formularz tworzenia nowej inwestycji.
     */
    public function create(Request $request)
    {
        // Sprawdź czy użytkownik może tworzyć inwestycje
        $this->authorize('create', Investment::class);
        
        // Jeśli przekazano ID projektu, pobierz projekt do kontekstu
        $project = null;
        if ($request->has('project_id') && is_numeric($request->project_id)) {
            $project = Project::findOrFail($request->project_id);
            
            // Sprawdź czy projekt jest aktywny
            if (!$project->isActive()) {
                return redirect()->route('projects.show', $project)
                    ->with('error', __('You can only invest in active projects'));
            }
        }
        
        // Jeśli nie przekazano projektu, pobierz wszystkie dostępne projekty
        $availableProjects = null;
        if (!$project) {
            $availableProjects = Project::where('status', 'active')->get();
            
            if ($availableProjects->isEmpty()) {
                return redirect()->route('projects.index')
                    ->with('info', __('There are currently no projects available for investment'));
            }
        }
        
        return view('investments.create', compact('project', 'availableProjects'));
    }

    /**
     * Zapisuje nową inwestycję w bazie danych.
     */
    public function store(Request $request)
    {
        // Sprawdź czy użytkownik może tworzyć inwestycje
        $this->authorize('create', Investment::class);
        
        // Walidacja danych
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0',
            'contact_preference' => 'required|string|in:email,phone,meeting',
            'contact_details' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Pobierz projekt
        $project = Project::findOrFail($validated['project_id']);
        
        // Sprawdź czy projekt jest aktywny
        if (!$project->isActive()) {
            return redirect()->route('projects.show', $project)
                ->with('error', __('You can only express interest in active projects'));
        }
        
        // Sprawdź czy kwota inwestycji jest wystarczająca
        if ($validated['amount'] < $project->min_investment) {
            return redirect()->route('investments.create', ['project_id' => $project->id])
                ->with('error', __('The minimum investment amount is :amount PLN', ['amount' => $project->min_investment]))
                ->withInput();
        }
        
        // Utwórz inwestycję
        $investment = Investment::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'amount' => $validated['amount'],
            'status' => 'interested',
            'contact_preference' => $validated['contact_preference'],
            'contact_details' => $validated['contact_details'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Wysyłamy powiadomienie do właściciela projektu, o ile istnieje
        if ($project->owner) {
            $project->owner->notify(new NewInvestmentNotification($investment));
        }
        
        return redirect()->route('investments.show', $investment)
                         ->with('success', __('Your interest has been registered'));
    }

    /**
     * Wyświetla szczegóły inwestycji.
     */
    public function show(Investment $investment)
    {
        \Illuminate\Support\Facades\Log::info('InvestmentController@show - rozpoczęcie', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'user_role' => auth()->user()->role,
            'investment_id' => $investment->id,
            'investment_status' => $investment->status,
            'project_id' => $investment->project_id,
            'project_owner_id' => $investment->project->owner_id,
            'is_project_owner' => (auth()->id() === $investment->project->owner_id) ? 'TAK' : 'NIE',
            'is_admin' => auth()->user()->isAdmin() ? 'TAK' : 'NIE'
        ]);
        
        try {
            // Sprawdź czy użytkownik może oglądać szczegóły inwestycji
            $this->authorize('view', $investment);
            
            \Illuminate\Support\Facades\Log::info('InvestmentController@show - autoryzacja pomyślna');
            
            // Pobierz inwestycję z relacjami
            $investment->load(['project', 'user']);
            
            return view('investments.show', compact('investment'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('InvestmentController@show - błąd autoryzacji', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Wyświetla formularz edycji inwestycji.
     */
    public function edit(Investment $investment)
    {
        // Sprawdź czy użytkownik może edytować tę inwestycję
        $this->authorize('update', $investment);
        
        // Pobierz inwestycję z relacjami
        $investment->load(['project', 'user']);
        
        return view('investments.edit', compact('investment'));
    }

    /**
     * Aktualizuje inwestycję w bazie danych.
     */
    public function update(Request $request, Investment $investment)
    {
        // Sprawdź czy użytkownik może aktualizować tę inwestycję
        $this->authorize('update', $investment);
        
        // Walidacja danych
        $validated = $request->validate([
            'status' => 'sometimes|required|in:interested,in_talks,contract_signed',
            'amount' => 'sometimes|required|numeric|min:0',
            'contact_preference' => 'sometimes|required|string|in:email,phone',
            'contact_details' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Aktualizuj inwestycję
        $investment->update($validated);
        
        return redirect()->route('investments.show', $investment)
                         ->with('success', __('Investment updated successfully'));
    }

    /**
     * Usuwa inwestycję.
     */
    public function destroy(Investment $investment)
    {
        // Sprawdź czy użytkownik może usunąć tę inwestycję
        $this->authorize('delete', $investment);
        
        // Sprawdź czy inwestycja może być anulowana
        if (!in_array($investment->status, ['interested', 'in_talks'])) {
            return redirect()->route('investments.show', $investment)
                ->with('error', __('Investment cannot be cancelled in its current status'));
        }
        
        // Usuń inwestycję
        $investment->delete();
        
        return redirect()->route('investments.index')
                         ->with('success', __('Investment has been successfully cancelled'));
    }

    /**
     * Zmienia status inwestycji.
     */
    public function changeStatus(Request $request, Investment $investment)
    {
        // Sprawdź czy użytkownik może zmieniać status inwestycji
        $this->authorize('updateStatus', $investment);
        
        // Walidacja danych
        $validated = $request->validate([
            'status' => 'required|in:interested,in_talks,contract_signed,finalized,rejected,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Zapisz poprzedni status
        $oldStatus = $investment->status;
        
        // Aktualizuj status i notatki
        $investment->status = $validated['status'];
        if (isset($validated['notes'])) {
            $investment->status_notes = $validated['notes'];
        }
        $investment->save();
        
        // Pobieramy projekt
        $project = $investment->project;
        
        // Jeśli status zmienił się na "contract_signed" (umowa podpisana), zwiększamy kwotę zebraną w projekcie
        if ($validated['status'] === 'contract_signed' && $oldStatus !== 'contract_signed') {
            $project->current_amount = $project->current_amount + $investment->amount;
            $project->save();
            
            \Illuminate\Support\Facades\Log::info('Zwiększono kwotę zebraną projektu', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'previous_amount' => $project->current_amount - $investment->amount,
                'new_amount' => $project->current_amount,
                'investment_amount' => $investment->amount,
                'investment_id' => $investment->id
            ]);
        }
        // Jeśli status zmienił się z "contract_signed" na inny, zmniejszamy kwotę zebraną w projekcie
        elseif ($oldStatus === 'contract_signed' && $validated['status'] !== 'contract_signed') {
            $project->current_amount = max(0, $project->current_amount - $investment->amount);
            $project->save();
            
            \Illuminate\Support\Facades\Log::info('Zmniejszono kwotę zebraną projektu', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'previous_amount' => $project->current_amount + $investment->amount,
                'new_amount' => $project->current_amount,
                'investment_amount' => $investment->amount,
                'investment_id' => $investment->id
            ]);
        }

        return redirect()->route('investments.show', $investment)
                         ->with('success', __('Investment status has been updated'));
    }
}
