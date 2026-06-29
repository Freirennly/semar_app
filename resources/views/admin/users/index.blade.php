<x-layouts.app :title="'Manajemen User'">
    <div class="flex items-center justify-between mb-12 animate-fade-in">
        <div>
            <h1 class="text-3xl md:text-[36px] font-bold text-text tracking-tight leading-[1.3]">Manajemen User</h1>
            <p class="text-sm font-medium text-text-secondary mt-2 leading-[1.2]">Kelola pengguna dan peran dalam sistem SEMAR.</p>
        </div>
    </div>

    <div class="card p-6 mb-12 border-primary/10 shadow-sm">
        <h2 class="text-[24px] font-semibold text-text leading-[1.4] mb-4">Tambah User Baru</h2>
        @if($errors->any())
            <div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" required class="input-field" placeholder="Nama lengkap user">
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" id="email" required class="input-field" placeholder="user@example.com">
            </div>
            <div>
                <label for="password" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" name="password" id="password" required class="input-field" placeholder="••••••••">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="role" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Role</label>
                    <select name="role" id="role" required class="input-field">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary shrink-0 py-2.5 px-6 shadow-sm hover:shadow-primary/20">Tambah</button>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-border bg-bg/30">
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Nama</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] hidden sm:table-cell">Email</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em]">Role</th>
                        <th class="px-6 py-4 text-[12px] font-semibold text-text-secondary uppercase tracking-[0.05em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($users as $u)
                    <tr class="hover:bg-soft-surface/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-soft-surface flex items-center justify-center text-primary font-bold text-xs">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-text group-hover:text-primary transition-colors">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-text-secondary hidden sm:table-cell">{{ $u->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-soft-surface text-primary border border-primary/20 uppercase tracking-tighter">{{ $u->roles->first()?->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline shadow-confirm">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger hover:text-danger/80 text-xs font-bold hover:underline" onclick="return confirm('Hapus user {{ $u->name }}? Konfirmasi diperlukan.')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-border bg-bg/10">{{ $users->links() }}</div>
    </div>
</x-layouts.app>
