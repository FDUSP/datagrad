<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Replicado\Graduacao;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Uspdev\UspTheme\Facades\UspTheme;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            UspTheme::activeUrl('roles');
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('roles');

        $roles = Role::where('name', 'not like', 'disciplinas%')->get();

        $roleCG = Role::where('name', 'CG')->first();
        $roleCC = Role::where('name', 'CC')->first();
        $roleBiblioteca = Role::where('name', 'biblioteca')->first();
        $departamentos = [];

        // cria as permissions referentes aos departamentos no formato disciplinas_xxx,
        // onde xxx é o prefixo da disciplina
        foreach (Graduacao::listarPrefixosDisciplinas() as $prefixo) {
            $role = Role::firstOrCreate(['name' => 'disciplinas_' . $prefixo]);
            $role->givePermissionTo('disciplina-chefe');
            $departamentos[] = Role::firstOrCreate(['name' => 'disciplinas_' . $prefixo]);
        }

        return view('roles.index', compact('roles', 'departamentos', 'roleCG', 'roleCC', 'roleBiblioteca'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * Este update não segue o padrão do laravel. Usa o $role_name ao invés do id
     */
    public function update(Request $request, string $role_name)
    {
        $this->authorize('roles');

        if ($add = $request->codpes_add) {
            $user = User::findOrCreateFromReplicado($add);
            if ($user) {
                $user->assignRole($role_name);
            }
        }

        if ($rem = $request->codpes_rem) {
            $user = User::where('codpes', $rem)->first();
            if ($user) {
                $user->removeRole($role_name);
            }
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
