<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Wyświetla listę projektów.
     */
    public function index(Request $request)
    {
        $query = Project::query();
        
        // Filtrowanie według statusu
        if ($request->has('status') && in_array($request->status, ['draft', 'active', 'funded', 'completed'])) {
            $query->where('status', $request->status);
        }
        
        // Filtrowanie według poziomu ryzyka
        if ($request->has('risk_level') && in_array($request->risk_level, ['low', 'medium', 'high'])) {
            $query->where('risk_level', $request->risk_level);
        }
        
        // Filtrowanie według minimalnej inwestycji
        if ($request->has('min_investment') && is_numeric($request->min_investment)) {
            $query->where('min_investment', '<=', $request->min_investment);
        }
        
        // Wyszukiwanie
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Sortowanie
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $allowedSortFields = ['name', 'target_amount', 'current_amount', 'start_date', 'end_date', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }
        
        // Pobierz paginowane projekty
        $projects = $query->with('owner')->paginate(10);
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Wyświetla formularz tworzenia nowego projektu.
     */
    public function create()
    {
        // Tylko administratorzy i managerowie mogą tworzyć projekty
        $this->authorize('create', Project::class);
        
        return view('projects.create');
    }

    /**
     * Zapisuje nowy projekt w bazie danych.
     */
    public function store(Request $request)
    {
        // Tylko administratorzy i managerowie mogą tworzyć projekty
        $this->authorize('create', Project::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'min_investment' => 'required|numeric|min:100|lte:target_amount',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'returns_projection' => 'required|numeric|min:0|max:100',
            'risk_level' => 'required|in:low,medium,high',
        ]);
        
        // Ustaw domyślne wartości
        $validated['owner_id'] = Auth::id();
        $validated['current_amount'] = 0;
        $validated['status'] = 'draft';
        
        $project = Project::create($validated);
        
        return redirect()->route('projects.show', $project)
                         ->with('success', 'Projekt został utworzony pomyślnie.');
    }

    /**
     * Wyświetla szczegóły projektu.
     */
    public function show(Project $project)
    {
        // Każdy zalogowany użytkownik może zobaczyć aktywne projekty
        // Tylko właściciel, administratorzy i managerowie mogą zobaczyć szkice
        if ($project->status === 'draft') {
            $this->authorize('view', $project);
        }
        
        // Pobierz inwestycje związane z projektem
        $project->load('owner');
        
        return view('projects.show', compact('project'));
    }

    /**
     * Wyświetla formularz edycji projektu.
     */
    public function edit(Project $project)
    {
        // Tylko właściciel projektu, administratorzy i managerowie mogą edytować projekt
        $this->authorize('update', $project);
        
        return view('projects.edit', compact('project'));
    }

    /**
     * Aktualizuje projekt w bazie danych.
     */
    public function update(Request $request, Project $project)
    {
        // Tylko właściciel projektu, administratorzy i managerowie mogą aktualizować projekt
        $this->authorize('update', $project);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'min_investment' => 'required|numeric|min:100|lte:target_amount',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'returns_projection' => 'required|numeric|min:0|max:100',
            'risk_level' => 'required|in:low,medium,high',
        ]);
        
        // Tylko administratorzy mogą zmienić status projektu
        if (Auth::user()->isAdmin() && $request->has('status')) {
            $validated['status'] = $request->status;
        }
        
        $project->update($validated);
        
        return redirect()->route('projects.show', $project)
                         ->with('success', 'Projekt został zaktualizowany pomyślnie.');
    }

    /**
     * Usuwa projekt z bazy danych.
     */
    public function destroy(Project $project)
    {
        // Tylko właściciel projektu i administratorzy mogą usunąć projekt
        $this->authorize('delete', $project);
        
        $project->delete();
        
        return redirect()->route('projects.index')
                         ->with('success', 'Projekt został usunięty pomyślnie.');
    }
    
    /**
     * Zmienia status projektu.
     */
    public function changeStatus(Request $request, Project $project)
    {
        // Tylko administratorzy mogą zmieniać status projektu
        $this->authorize('changeStatus', $project);
        
        $validated = $request->validate([
            'status' => 'required|in:draft,active,funded,completed',
        ]);
        
        $project->status = $validated['status'];
        $project->save();
        
        return redirect()->route('projects.show', $project)
                         ->with('success', 'Status projektu został zmieniony.');
    }
}
