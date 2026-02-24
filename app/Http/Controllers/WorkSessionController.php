<?php

namespace App\Http\Controllers;

use App\Models\WorkSession;
use Illuminate\Http\Request;

class WorkSessionController extends Controller
{
    public function index()
    {
        $sessions = WorkSession::withCount('attendances')->orderBy('date', 'desc')->orderBy('start_time', 'desc')->get();
        return view('admin.sessions.index', compact('sessions'));
    }

    public function show(WorkSession $workSession)
    {
        $workSession->load('attendances.user');
        return response()->json([
            'title' => $workSession->title,
            'date' => $workSession->date->format('d M Y'),
            'time' => \Carbon\Carbon::parse($workSession->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($workSession->end_time)->format('H:i'),
            'attendees' => $workSession->attendances->map(function($attendance) {
                return [
                    'name' => $attendance->user->name,
                    'time_in' => \Carbon\Carbon::parse($attendance->time_in)->format('H:i'),
                    'status' => $attendance->status,
                    'photo' => $attendance->photo_in ? asset('storage/' . $attendance->photo_in) : null
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'wage' => 'required|numeric|min:0',
        ]);

        WorkSession::create($validated + ['is_active' => false]);

        return redirect()->route('admin.sessions')->with('success', 'Sesi kerja berhasil dibuat.');
    }
    
    // Add update method if needed for editing session details later, currently user only asked for create modal
    // but for completeness we can support update if modal supports it.
    // For now, index view only shows create modal based on request "bikin modal".

    public function toggleStatus(WorkSession $workSession)
    {
        $workSession->update(['is_active' => !$workSession->is_active]);
        $status = $workSession->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()->with('success', "Sesi berhasil $status.");
    }
    
    public function destroy(WorkSession $workSession)
    {
        $workSession->delete();
        return redirect()->back()->with('success', 'Sesi kerja berhasil dihapus.');
    }
}
