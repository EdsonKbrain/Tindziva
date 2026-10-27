<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->withCount('posts')
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$request->string('q').'%')
                ->orWhere('email', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roles' => Role::cases()]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.form', $this->formData(new User(['role' => Role::Author, 'is_active' => true])));
    }

    /**
     * Creates the account and, when no password is given, emails a secure link to define one.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $sendInvite = blank($request->input('password'));

        $user = DB::transaction(function () use ($request, $sendInvite): User {
            $user = new User($request->payload());
            $user->password = Hash::make($sendInvite ? Str::password(40) : $request->input('password'));
            $user->email_verified_at = now();
            $user->save();

            return $user;
        });

        $message = 'Utilizador criado com sucesso.';

        if ($sendInvite) {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);
            $message .= $status === Password::RESET_LINK_SENT
                ? ' Foi enviado um e-mail para definir a palavra-passe.'
                : ' Não foi possível enviar o e-mail de convite; verifique a configuração SMTP.';
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.form', $this->formData($user));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->payload());

        if (filled($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
            $user->setRememberToken(Str::random(60));
        }

        $user->save();

        if (! $user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilizador actualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        if ($user->isSuperAdmin() && User::where('role', Role::SuperAdmin)->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Não é possível eliminar o último superadministrador activo.');
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilizador eliminado.');
    }

    public function sendResetLink(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            $status === Password::RESET_LINK_SENT ? 'Ligação de redefinição enviada.' : __($status),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(User $user): array
    {
        return [
            'user' => $user,
            'roles' => array_values(array_filter(Role::cases(), fn (Role $role) => request()->user()->can('assignRole', [User::class, $role]))),
            'permissions' => Permission::cases(),
        ];
    }
}
