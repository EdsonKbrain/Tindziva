<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared behaviour for contact messages and quote requests.
 */
abstract class LeadController extends Controller
{
    /**
     * @return class-string<Model>
     */
    abstract protected function model(): string;

    abstract protected function view(): string;

    abstract protected function routePrefix(): string;

    /**
     * @return array<string, callable(Model): mixed>
     */
    abstract protected function exportColumns(): array;

    public function index(Request $request): View
    {
        $model = $this->model();

        $leads = $model::query()
            ->with($this->relations())
            ->search($request->input('q'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = $model::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view("admin.{$this->view()}.index", [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
            'counts' => $counts,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function showLead(Model $lead): View
    {
        $lead->loadMissing($this->relations());

        return view("admin.{$this->view()}.show", [
            'lead' => $lead,
            'statuses' => LeadStatus::cases(),
            'users' => $this->assignableUsers(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function updateLead(LeadUpdateRequest $request, Model $lead): RedirectResponse
    {
        $lead->update($this->mapUpdate($request->validated(), $request));

        return back()->with('success', 'Pedido actualizado.');
    }

    public function destroyLead(Model $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route("admin.{$this->routePrefix()}.index")->with('success', 'Registo eliminado.');
    }

    public function export(Request $request): StreamedResponse
    {
        $model = $this->model();
        $columns = $this->exportColumns();

        return response()->streamDownload(function () use ($model, $columns, $request): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_keys($columns), escape: '');

            $model::query()
                ->with($this->relations())
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
                ->latest()
                ->chunk(500, function (Collection $rows) use ($handle, $columns): void {
                    foreach ($rows as $row) {
                        fputcsv($handle, array_map(fn (callable $column) => $this->csvSafe($column($row)), $columns), escape: '');
                    }
                });

            fclose($handle);
        }, $this->routePrefix().'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return list<string>
     */
    protected function relations(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mapUpdate(array $data, Request $request): array
    {
        return $data;
    }

    /**
     * @return Collection<int, User>
     */
    private function assignableUsers(): Collection
    {
        return User::where('is_active', true)->orderBy('name')->get()
            ->filter(fn (User $user): bool => $user->hasPermission(Permission::ManageLeads))
            ->values();
    }

    /**
     * Prevent spreadsheet formula injection in exported values.
     */
    private function csvSafe(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
