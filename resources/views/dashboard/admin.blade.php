<x-layouts.app :title="'Dashboard Admin'">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-text">Dashboard Admin</h2>
        <p class="text-sm text-text-secondary mt-1">Gambaran umum sistem dan manajemen pengguna.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        @foreach($metrics as $m)
            <x-metric-card :label="$m['label']" :value="$m['value']" :color="$m['color']" />
        @endforeach
    </div>
    <div class="card">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
            <h3 class="text-base font-semibold text-text">Daftar Pengguna</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary hover:text-primary-hover font-medium">Kelola →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-text-muted text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium hidden sm:table-cell">Email</th>
                    <th class="px-6 py-3 font-medium">Role</th>
                </tr></thead>
                <tbody class="divide-y divide-border">
                    @foreach($users->take(10) as $u)
                    <tr class="hover:bg-soft-surface/30 transition-colors">
                        <td class="px-6 py-3 font-medium text-text">{{ $u->name }}</td>
                        <td class="px-6 py-3 text-text-secondary hidden sm:table-cell">{{ $u->email }}</td>
                        <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-soft-surface text-primary border border-primary/20 capitalize">{{ $u->roles->first()?->name ?? '-' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
