<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\FullboardMeeting;
use Illuminate\Http\Request;

class FullboardMeetingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = \App\Models\FullboardMeeting::with('submission')->orderBy('scheduled_at', 'desc');

        // Reviewer hanya melihat jadwal proposal yang pernah ia review
        if ($user->hasRole('reviewer') && !$user->hasRole('sekretariat') && !$user->hasRole('ketua')) {
            $query->whereHas('submission.reviews', function ($q) use ($user) {
                $q->where('reviewer_id', $user->id);
            });
        } elseif ($user->hasRole('sekretariat')) {
            $query->whereHas('submission', function ($q) use ($user) {
                $q->where('secretary_id', $user->id);
            });
        }

        $meetings = $query->paginate(15);
        return view('fullboard.index', compact('meetings'));
    }

    public function create(Submission $submission)
    {
        // Must be secretariat
        $this->authorize('viewAny', \App\Models\Decision::class);
        if ($submission->secretary_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak membuat jadwal Fullboard untuk proposal ini.');
        }

        // Cannot schedule if already exists
        if ($submission->fullboardMeeting) {
            return redirect()->route('decisions.show', $submission)
                ->with('error', 'Jadwal Fullboard sudah ada.');
        }

        // Must have at least one RECOMMEND_FULLBOARD
        $hasFullboard = $submission->reviews->where('recommendation', \App\Enums\Recommendation::RECOMMEND_FULLBOARD)->isNotEmpty();
        if (!$hasFullboard) {
            return redirect()->route('decisions.show', $submission)
                ->with('error', 'Tidak ada rekomendasi Fullboard untuk proposal ini.');
        }

        return view('fullboard.create', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        $this->authorize('viewAny', \App\Models\Decision::class);
        if ($submission->secretary_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak membuat jadwal Fullboard untuk proposal ini.');
        }

        // 0. Pastikan proposal memiliki minimal satu review dengan RECOMMEND_FULLBOARD
        $hasFullboard = $submission->reviews()->where('recommendation', \App\Enums\Recommendation::RECOMMEND_FULLBOARD)->exists();
        if (!$hasFullboard) {
            abort(403, 'Proposal ini tidak direkomendasikan untuk Sidang Fullboard oleh reviewer mana pun.');
        }

        // 1. Pastikan satu proposal hanya boleh memiliki satu FullboardMeeting.
        if (\App\Models\FullboardMeeting::where('submission_id', $submission->id)->exists()) {
            return redirect()->route('decisions.show', $submission)
                ->with('error', 'Jadwal Fullboard sudah ada.');
        }

        $validated = $request->validate([
            'meeting_type' => 'required|in:OFFLINE,ONLINE,HYBRID',
            'scheduled_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'required_if:meeting_type,OFFLINE|required_if:meeting_type,HYBRID|nullable|string|max:255',
            'meeting_url' => 'required_if:meeting_type,ONLINE|required_if:meeting_type,HYBRID|nullable|url|max:255',
            'agenda' => 'required|string',
            'notes' => 'nullable|string',
        ], [
            'location.required_if' => 'Lokasi rapat wajib diisi untuk jenis rapat Offline dan Hybrid.',
            'meeting_url.required_if' => 'Link meeting wajib diisi untuk jenis rapat Online dan Hybrid.',
        ]);

        // Parsing waktu dengan asumsi input dari pengguna adalah Waktu Jakarta (WIB)
        $scheduledAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['scheduled_date'] . ' ' . $validated['start_time'], 'Asia/Jakarta')->setTimezone('UTC');
        
        // 3. scheduled_at tidak boleh sebelum waktu sekarang
        if ($scheduledAt->isPast()) {
            return back()->withInput()->withErrors(['scheduled_date' => 'Waktu jadwal tidak boleh sebelum waktu sekarang.']);
        }

        $endAt = null;
        if (!empty($validated['end_time'])) {
            $endAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['scheduled_date'] . ' ' . $validated['end_time'], 'Asia/Jakarta')->setTimezone('UTC');
            
            // 4. Jika end_at diisi, pastikan end_at > scheduled_at
            if ($endAt->lessThanOrEqualTo($scheduledAt)) {
                return back()->withInput()->withErrors(['end_time' => 'Jam selesai harus lebih besar dari jam mulai.']);
            }
        }

        // 5. Pastikan submission_id yang dikirim benar-benar ada dilakukan oleh implicit Route Model Binding (Submission $submission)
        // namun untuk extra layer, karena kita pakai instance langsung:
        if (!$submission->exists) {
            abort(404, 'Submission not found.');
        }

        $meeting = FullboardMeeting::create([
            'submission_id' => $submission->id,
            'meeting_type' => $validated['meeting_type'],
            'scheduled_at' => $scheduledAt,
            'end_at' => $endAt,
            'location' => $validated['location'],
            'meeting_url' => $validated['meeting_url'],
            'agenda' => $validated['agenda'],
            'notes' => $validated['notes'],
            // 2. created_by selalu menggunakan auth()->id()
            'created_by' => auth()->id(),
        ]);

        // Kirim Notifikasi
        $recipients = \App\Models\User::role('reviewer')->get();
        $ketua = \App\Models\User::role('ketua')->get();
        
        $allRecipients = $recipients->merge($ketua)->unique('id')->reject(function ($user) {
            return $user->id === auth()->id(); // Sekretariat pembuat tidak perlu
        });
        
        \Illuminate\Support\Facades\Notification::send($allRecipients, new \App\Notifications\FullboardMeetingScheduled($meeting));

        return redirect()->route('decisions.show', $submission)
            ->with('success', 'Jadwal Sidang Fullboard berhasil dibuat.');
    }
}
