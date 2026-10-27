<x-admin.layout title="Actividade">
    <x-admin.page-header title="Registo de actividades" description="Acções relevantes realizadas no painel." />

    <x-admin.filters placeholder="Pesquisar descrição…">
        <select name="user" class="admin-input sm:w-48" aria-label="Utilizador">
            <option value="">Todos os utilizadores</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(request('user') == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <select name="action" class="admin-input sm:w-44" aria-label="Acção">
            <option value="">Todas as acções</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
            @endforeach
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Data</th><th class="px-5 py-3">Utilizador</th><th class="px-5 py-3">Acção</th><th class="px-5 py-3">Descrição</th><th class="px-5 py-3">IP</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-5 py-3 whitespace-nowrap text-gray-600">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $log->user?->name ?? 'Sistema' }}</td>
                            <td class="px-5 py-3"><x-admin.badge :color="match ($log->action) { 'deleted', 'login_failed' => 'red', 'created' => 'green', 'login', 'logout' => 'blue', default => 'gray' }">{{ $log->action }}</x-admin.badge></td>
                            <td class="px-5 py-3 text-gray-700">
                                {{ $log->description }}
                                @if (! empty($log->properties['fields']))
                                    <span class="block text-xs text-gray-400">Campos: {{ implode(', ', $log->properties['fields']) }}</span>
                                @endif
                                @if (! empty($log->properties['email']))
                                    <span class="block text-xs text-gray-400">{{ $log->properties['email'] }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <x-admin.empty :colspan="5" icon="activity" title="Sem actividade registada" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin.layout>
