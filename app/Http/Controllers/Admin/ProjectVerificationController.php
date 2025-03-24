<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectVerificationController extends Controller
{
    /**
     * Wyświetla listę projektów do weryfikacji.
     */
    public function index()
    {
        $projects = Project::where('status', 'draft')
                          ->with('owner')
                          ->latest()
                          ->paginate(10);
        
        return view('admin.projects.verification', compact('projects'));
    }
    
    /**
     * Akceptuje projekt.
     */
    public function accept(Project $project)
    {
        $this->authorize('changeStatus', $project);
        
        $project->status = 'active';
        $project->save();
        
        // TODO: Dodać powiadomienie dla właściciela projektu
        
        return redirect()->route('admin.projects.verification')
                         ->with('success', 'Projekt został zaakceptowany.');
    }
    
    /**
     * Odrzuca projekt.
     */
    public function reject(Project $project)
    {
        $this->authorize('changeStatus', $project);
        
        $project->status = 'cancelled';
        $project->save();
        
        // TODO: Dodać powiadomienie dla właściciela projektu
        
        return redirect()->route('admin.projects.verification')
                         ->with('success', 'Projekt został odrzucony.');
    }
} 