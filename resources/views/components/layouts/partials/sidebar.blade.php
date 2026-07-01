{{-- Logo --}}
<div class="h-16 flex items-center px-6 border-b border-border">
    <span class="text-xl font-bold text-primary tracking-tight">SEMAR</span>
    <span class="text-[10px] text-text-muted ml-1.5 mt-1">v1.0</span>
</div>

{{-- Navigation --}}
<nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto" aria-label="Sidebar">
    @php $r = request()->route()?->getName() ?? ''; @endphp

    {{-- Student --}}
    @role('student')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $r === 'dashboard' ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Overview Dashboard">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
        Overview
    </a>
    <a href="{{ route('submissions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'submissions') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Pengajuan Saya">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        Pengajuan Saya
    </a>
    <a href="{{ route('ethical-clearance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'ethical-clearance') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Ethical Clearance">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-6.75a1.125 1.125 0 00-1.125 1.125v3.375m9 0M9 10.5c.621 0 1.125-.504 1.125-1.125v-3a1.125 1.125 0 00-1.125-1.125H3.75A1.125 1.125 0 002.625 7.5v3c0 .621.504 1.125 1.125 1.125H9zM21.375 7.5v3c0 .621-.504 1.125-1.125 1.125H15c-.621 0-1.125-.504-1.125-1.125v-3c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125z"/></svg>
        Ethical Clearance
    </a>
    @endrole

    {{-- Reviewer --}}
    @role('reviewer')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $r === 'dashboard' ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Overview Dashboard">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
        Overview
    </a>
    <a href="{{ route('reviews.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'reviews') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Review Saya">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
        Review Saya
    </a>
    @endrole

    {{-- Ketua --}}
    @role('ketua')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $r === 'dashboard' ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Overview Dashboard">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
        Overview
    </a>
    <a href="{{ route('chairman.monitoring') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'chairman.monitoring') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Pemantauan & Riwayat">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Pemantauan & Riwayat
    </a>
    @endrole

    {{-- Sekretariat --}}
    @role('sekretariat')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $r === 'dashboard' ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Overview Dashboard">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
        Overview
    </a>
    <a href="{{ route('doccheck.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'doccheck') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Cek Dokumen">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4"/></svg>
        Cek Dokumen
    </a>
    <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'assignments') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Penugasan Reviewer">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        Penugasan Reviewer
    </a>
    <a href="{{ route('decisions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'decisions') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Keputusan">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z"/></svg>
        Keputusan
    </a>
    @endrole

    {{-- Admin --}}
    @role('admin')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $r === 'admin.dashboard' ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Overview Dashboard">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-1.25-18v-2.25z"/></svg>
        Overview
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.users') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen User">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Manajemen User
    </a>
    <a href="{{ route('admin.proposals.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.proposals') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen Pengajuan">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Manajemen Pengajuan
    </a>
    <a href="{{ route('admin.reviewers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.reviewers') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen Reviewer">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Manajemen Reviewer
    </a>
    <a href="{{ route('admin.secretariat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.secretariat') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen Sekretaris">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Manajemen Sekretaris
    </a>
    <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.announcements') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen Pengumuman">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.297A1.705 1.705 0 019.336 21H4.104a.71.71 0 01-.7-.718V4.731a.71.71 0 01.7-.717h5.232c.9 0 1.631.733 1.631 1.631zM11 5.882c0-.9.731-1.631 1.631-1.631h5.232c.386 0 .7.314.7.717v15.849a.71.71 0 01-.701.718h-5.232a1.705 1.705 0 01-1.664-1.703V5.882zM7 10h2m-2 3h2m7-3h2m-2 3h2"/></svg>
        Manajemen Pengumuman
    </a>
    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.reports') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Laporan & Statistik">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
        Laporan & Statistik
    </a>
    <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.templates') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Manajemen Template Dokumen">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5h10.5a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"/></svg>
        Manajemen Template
    </a>
    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ str_starts_with($r, 'admin.settings') ? 'bg-primary-light text-primary font-semibold' : 'text-text-secondary hover:bg-soft-surface hover:text-primary' }}" aria-label="Setting">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Setting
    </a>
    @endrole
</nav>

{{-- Sidebar Footer: 2-Line Actions --}}
<div class="p-4 border-t border-border bg-surface mt-auto">
    {{-- Line 1: Kembali ke Beranda (13px, Regular) --}}
    <a href="{{ route('landing') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-normal text-text-secondary hover:bg-soft-surface hover:text-primary transition-colors">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
        Kembali ke Beranda
    </a>

    {{-- Line 2: Keluar (14px, SemiBold) — 16px spacing from Line 1 --}}
    <form method="POST" action="{{ route('logout') }}" class="block w-full mt-4">
        @csrf
        <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[14px] font-semibold text-text-secondary hover:bg-danger-bg hover:text-danger w-full transition-colors" aria-label="Keluar dari Sistem">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
            </svg>
            Keluar
        </button>
    </form>
</div>