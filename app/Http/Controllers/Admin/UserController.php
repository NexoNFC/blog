<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\AdministratorIntegrityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private AdministratorIntegrityService $administrators) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::query()->with('roles')->orderBy('name')->get(),
            'activeAdministratorCount' => $this->administrators->activeAdministratorCount(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);
        $this->authorize('manageRoles', User::class);

        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->email_verified_at = now();
        $user->save();

        $user->syncRoles([$request->validated('role')]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'El usuario se creó correctamente.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('roles');

        return view('admin.users.edit', [
            'user' => $user,
            'isLastActiveAdministrator' => $this->administrators->isLastActiveAdministrator($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $this->authorize('manageRoles', User::class);

        $role = $request->validated('role');
        $isActive = $request->boolean('is_active');

        $this->administrators->ensureCanAssignRole($user, $role);
        $this->administrators->ensureCanChangeActiveState($user, $isActive);

        $user->fill([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'is_active' => $isActive,
        ]);

        if (filled($request->validated('password'))) {
            $user->password = $request->validated('password');
        }

        $user->save();
        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'El usuario se actualizó correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->administrators->ensureCanDelete($user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'El usuario se eliminó correctamente.');
    }
}
