<x-layouts.app :title="'Notifikasi'">

{{-- Page Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-4 h-[2px]" style="background:#463EE3;"></div>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color:#463EE3">Informasi Sistem</p>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color:#0F0E2E">Notifikasi</h1>
        <p class="text-sm font-light mt-1" style="color:#5A587A">Berikut adalah daftar aktivitas dan perubahan status terbaru pada usulan Anda.</p>
    </div>
    
    @if(auth()->user()->unreadNotifications->isNotEmpty())
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs font-bold px-4 py-2.5 rounded-xl border flex items-center gap-1.5 transition-all duration-150"
                    style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white;
                           box-shadow:0 1px 4px rgba(70,62,227,0.08);"
                    onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3'; this.style.boxShadow='0 3px 10px rgba(70,62,227,0.25)'"
                    onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)'; this.style.boxShadow='0 1px 4px rgba(70,62,227,0.08)'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

{{-- Notifications List --}}
<div class="bg-white rounded-2xl border overflow-hidden"
     style="border-color:rgba(70,62,227,0.14);
            box-shadow: 0 2px 16px rgba(70,62,227,0.07);">

    @if($notifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background:#F5F5F5; border:1.5px dashed rgba(70,62,227,0.18);">
                <svg class="w-7 h-7" fill="none" stroke="#c4c2e0" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
            </div>
            <p class="text-sm font-semibold mb-1" style="color:#5A587A">Tidak ada notifikasi</p>
            <p class="text-xs font-light" style="color:#b0aec8">Anda akan menerima pemberitahuan di sini ketika ada update status baru.</p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Pemberitahuan Sistem';
                    $message = $data['message'] ?? '';
                    $actionUrl = $data['action_url'] ?? '#';
                @endphp
                <div class="p-5 flex items-start justify-between gap-4 transition-all duration-150 {{ $isUnread ? 'bg-[#463EE3]/[0.02]' : 'hover:bg-slate-50' }}">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        {{-- Unread indicator dot --}}
                        <div class="mt-2 flex-shrink-0 w-2.5 h-2.5 rounded-full {{ $isUnread ? 'bg-[#463EE3]' : 'bg-transparent' }}" 
                             title="{{ $isUnread ? 'Belum dibaca' : 'Sudah dibaca' }}"></div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold {{ $isUnread ? 'text-[#0F0E2E]' : 'text-[#5A587A]' }}">
                                {{ $title }}
                            </h3>
                            <p class="text-xs font-light text-[#5A587A] mt-1.5 leading-relaxed">
                                {{ $message }}
                            </p>
                            <span class="text-[10px] text-gray-400 mt-2 block">
                                {{ $notification->created_at->timezone('Asia/Jakarta')->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex items-center gap-2">
                        @if($isUnread)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border inline-flex items-center gap-1 transition-all duration-150"
                                        style="color:#463EE3; border-color:rgba(70,62,227,0.22); background:white;
                                               box-shadow:0 1px 4px rgba(70,62,227,0.08);"
                                        onmouseover="this.style.background='#463EE3'; this.style.color='white'; this.style.borderColor='#463EE3';"
                                        onmouseout="this.style.background='white'; this.style.color='#463EE3'; this.style.borderColor='rgba(70,62,227,0.22)';">
                                    Baca & Detail
                                </button>
                            </form>
                        @elseif($actionUrl && $actionUrl !== '#')
                            <a href="{{ $actionUrl }}" class="text-xs font-medium px-3 py-1.5 rounded-lg border inline-flex items-center gap-1 transition-all duration-150 text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 border-slate-200">
                                Buka Link
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

</x-layouts.app>
