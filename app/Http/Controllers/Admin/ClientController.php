<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientSegment;
use App\Enums\ClientType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::query()
            ->with('logo')
            ->withCount('projects')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('segment'), fn ($query) => $query->where('segment', $request->input('segment')))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.clients.index', ['clients' => $clients, 'types' => ClientType::cases(), 'segments' => ClientSegment::cases()]);
    }

    public function create(): View
    {
        return view('admin.clients.form', ['client' => new Client(['is_visible' => true, 'type' => ClientType::Client]), 'types' => ClientType::cases(), 'segments' => ClientSegment::cases()]);
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        Client::create($request->payload());

        return redirect()->route('admin.clients.index')->with('success', 'Cliente adicionado com sucesso.');
    }

    public function edit(Client $client): View
    {
        $client->load(['logo', 'projects']);

        return view('admin.clients.form', ['client' => $client, 'types' => ClientType::cases(), 'segments' => ClientSegment::cases()]);
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->payload());

        return redirect()->route('admin.clients.index')->with('success', 'Cliente actualizado com sucesso.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Cliente eliminado.');
    }

    /**
     * Persist a new display order from the drag-and-drop list.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', Rule::exists('clients', 'id')],
        ]);

        foreach ($validated['order'] as $position => $id) {
            Client::whereKey($id)->update(['sort_order' => $position]);
        }

        Client::first()?->touch();

        return back()->with('success', 'Ordem de apresentação actualizada.');
    }
}
