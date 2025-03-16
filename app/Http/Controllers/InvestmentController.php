<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
                    ->with('error', 'Możesz inwestować tylko w aktywne projekty.');
            }
        }
        
        // Jeśli nie przekazano projektu, pobierz wszystkie dostępne projekty
        $availableProjects = null;
        if (!$project) {
            $availableProjects = Project::where('status', 'active')->get();
            
            if ($availableProjects->isEmpty()) {
                return redirect()->route('projects.index')
                    ->with('info', 'Aktualnie nie ma dostępnych projektów do inwestowania.');
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
            'contact_preference' => 'required|string|in:email,phone',
            'contact_details' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Pobierz projekt
        $project = Project::findOrFail($validated['project_id']);
        
        // Sprawdź czy projekt jest aktywny
        if (!$project->isActive()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Możesz wyrazić zainteresowanie tylko aktywnymi projektami.');
        }
        
        // Sprawdź czy kwota inwestycji jest wystarczająca
        if ($validated['amount'] < $project->min_investment) {
            return redirect()->route('investments.create', ['project_id' => $project->id])
                ->with('error', "Minimalna kwota inwestycji to {$project->min_investment} zł.")
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
        
        return redirect()->route('investments.show', $investment)
                         ->with('success', 'Twoje zainteresowanie zostało zarejestrowane.');
    }

    /**
     * Wyświetla szczegóły inwestycji.
     */
    public function show(Investment $investment)
    {
        // Sprawdź czy użytkownik może oglądać szczegóły inwestycji
        $this->authorize('view', $investment);
        
        // Pobierz inwestycję z relacjami
        $investment->load(['project', 'user']);
        
        return view('investments.show', compact('investment'));
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
                         ->with('success', 'Inwestycja została zaktualizowana pomyślnie.');
    }

    /**
     * Anuluje inwestycję.
     */
    public function destroy(Investment $investment)
    {
        // Sprawdź czy użytkownik może anulować tę inwestycję
        $this->authorize('delete', $investment);
        
        // Sprawdź czy inwestycja może być anulowana
        if (!in_array($investment->status, ['interested', 'in_talks'])) {
            return redirect()->route('investments.show', $investment)
                ->with('error', 'Nie można anulować inwestycji w bieżącym statusie.');
        }
        
        // Anuluj inwestycję
        $investment->update(['status' => 'cancelled']);
        
        return redirect()->route('investments.index')
                         ->with('success', 'Inwestycja została anulowana.');
    }
    
    /**
     * Zmiana statusu inwestycji.
     */
    public function changeStatus(Request $request, Investment $investment)
    {
        // Sprawdź czy użytkownik może zmieniać status inwestycji
        $this->authorize('changeStatus', $investment);
        
        // Walidacja danych
        $validated = $request->validate([
            'status' => 'required|in:declared,paid,confirmed,cancelled',
        ]);
        
        // Stary status
        $oldStatus = $investment->status;
        
        // Nowy status
        $newStatus = $validated['status'];
        
        // Jeśli status się nie zmienił, nic nie rób
        if ($oldStatus === $newStatus) {
            return redirect()->route('investments.show', $investment)
                ->with('info', 'Status inwestycji nie został zmieniony.');
        }
        
        // Przeprowadź odpowiednie operacje zależnie od zmiany statusu
        
        // Jeśli zmiana na potwierdzoną
        if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed') {
            // Dodaj kwotę do projektu
            $project = $investment->project;
            $project->current_amount += $investment->amount;
            
            // Jeśli projekt został w pełni sfinansowany, zmień jego status
            if ($project->current_amount >= $project->target_amount) {
                $project->status = 'funded';
            }
            
            $project->save();
        }
        
        // Jeśli zmiana z potwierdzonej na inny status
        if ($oldStatus === 'confirmed' && $newStatus !== 'confirmed') {
            // Odejmij kwotę od projektu
            $project = $investment->project;
            $project->current_amount -= $investment->amount;
            
            // Jeśli projekt był w pełni sfinansowany, a teraz już nie jest, zmień jego status z powrotem na aktywny
            if ($project->status === 'funded' && $project->current_amount < $project->target_amount) {
                $project->status = 'active';
            }
            
            $project->save();
        }
        
        // Jeśli zmiana na anulowaną i była płatność, zwróć środki
        if ($newStatus === 'cancelled' && $oldStatus === 'paid') {
            $user = $investment->user;
            $user->wallet_balance += $investment->amount;
            $user->save();
        }
        
        // Aktualizuj status inwestycji
        $investment->status = $newStatus;
        $investment->save();
        
        return redirect()->route('investments.show', $investment)
                         ->with('success', 'Status inwestycji został zmieniony.');
    }
}
