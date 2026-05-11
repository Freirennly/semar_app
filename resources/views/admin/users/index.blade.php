<x-layouts.app :title="'Manajemen User'">
    <div class="flex items-center justify-between mb-6">
        <div><h2 class="text-xl font-bold text-text">Manajemen User</h2><p class="text-sm text-text-secondary mt-1">Kelola pengguna dan peran dalam sistem.</p></div>
    </div>
    <div class="card p-6 mb-6">
        <h3 class="text-base font-semibold text-text mb-4">Tambah User Baru</h3>
        @if($errors->any())<div class="mb-4 bg-danger-bg border border-danger/20 text-danger rounded-lg px-4 py-3 text-sm" role="alert"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">@csrf
            <div><label for="name" class="block text-xs font-medium text-text-secondary mb-1">Nama</label><input type="text" name="name" id="name" required class="input-field"></div>
            <div><label for="email" class="block text-xs font-medium text-text-secondary mb-1">Email</label><input type="email" name="email" id="email" required class="input-field"></div>
            <div><label for="password" class="block text-xs font-medium text-text-secondary mb-1">Password</label><input type="password" name="password" id="password" required class="input-field"></div>
            <div class="flex items-end gap-2">
                <div class="flex-1"><label for="role" class="block text-xs font-medium text-text-secondary mb-1">Role</label><select name="role" id="role" required class="input-field">@foreach($roles as $role)<option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>@endforeach</select></div>
                <button type="submit" class="btn-primary shrink-0">Tambah</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left text-text-muted text-xs uppercase tracking-wider border-b border-border"><th class="px-6 py-3 font-medium">Nama</th><th class="px-6 py-3 font-medium hidden sm:table-cell">Email</th><th class="px-6 py-3 font-medium">Role</th><th class="px-6 py-3 font-medium">Aksi</th></tr></thead>
        <tbody class="divide-y divide-border">@foreach($users as $u)<tr class="hover:bg-soft-surface/30 transition-colors"><td class="px-6 py-3 font-medium text-text">{{ $u->name }}</td><td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $u->email }}</td><td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-soft-surface text-primary border border-primary/20 capitalize">{{ $u->roles->first()?->name ?? '-' }}</span></td><td class="px-6 py-3"><form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline">@csrf @method('DELETE')<button type="submit" class="text-danger hover:text-danger/80 text-sm font-medium" onclick="return confirm('Hapus user {{ $u->name }}?')" aria-label="Hapus user {{ $u->name }}">Hapus</button></form></td></tr>@endforeach</tbody></table></div>
        <div class="px-6 py-4 border-t border-border">{{ $users->links() }}</div>
    </div>
</x-layouts.app>
