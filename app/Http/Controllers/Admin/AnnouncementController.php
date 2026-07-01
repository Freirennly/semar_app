<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::latest();

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where('title', 'like', "%{$searchTerm}%");
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'publish_date' => 'nullable|date',
        ]);

        if ($validated['status'] === 'published' && empty($validated['publish_date'])) {
            $validated['publish_date'] = now();
        }

        $announcement = Announcement::create($validated);

        if ($announcement->status === 'published') {
            $this->sendAnnouncementNotification($announcement);
        }

        Cache::forget('admin_reports_stats');

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'publish_date' => 'nullable|date',
        ]);

        $oldStatus = $announcement->status;

        if ($validated['status'] === 'published' && empty($validated['publish_date'])) {
            $validated['publish_date'] = now();
        } elseif ($validated['status'] === 'draft') {
            $validated['publish_date'] = null;
        }

        $announcement->update($validated);

        if ($oldStatus === 'draft' && $announcement->status === 'published') {
            $this->sendAnnouncementNotification($announcement);
        }

        Cache::forget('admin_reports_stats');

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        Cache::forget('admin_reports_stats');
        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function sendAnnouncementNotification(Announcement $announcement)
    {
        $users = \App\Models\User::where('is_active', true)->get();

        Notification::send($users, new AnnouncementNotification($announcement));
    }
}
