<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with('student')->latest();

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhereHas('student', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->paginate(15)->withQueryString();

        return view('admin.proposals.index', compact('proposals'));
    }

    public function show(Submission $proposal)
    {
        // For admin to see full details, could reuse existing submission show view
        // But the route is `admin.proposals.show`. We can just redirect to the main submission view if we want.
        return redirect()->route('submissions.show', $proposal);
    }

    public function edit(Submission $proposal)
    {
        return view('admin.proposals.edit', compact('proposal'));
    }

    public function update(Request $request, Submission $proposal)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            // Allow admin to also fix minor typos in title if needed
            'title' => 'required|string|max:255',
        ]);

        $proposal->update($validated);

        Cache::forget('admin_reports_stats');

        return redirect()->route('admin.proposals.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Submission $proposal)
    {
        $proposal->delete();
        
        Cache::forget('admin_reports_stats');
        
        return redirect()->route('admin.proposals.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
