<?php

namespace App\Http\Controllers;

use App\Http\Requests\VaultRequest;
use App\Http\Resources\VaultResource;
use App\Models\Vault;

class VaultController extends Controller
{
    public function index()
    {
        return VaultResource::collection(Vault::all());
    }

    public function store(VaultRequest $request)
    {
        return new VaultResource(Vault::create($request->validated()));
    }

    public function show(Vault $vault)
    {
        return new VaultResource($vault);
    }

    public function update(VaultRequest $request, Vault $vault)
    {
        $vault->update($request->validated());

        return new VaultResource($vault);
    }

    public function destroy(Vault $vault)
    {
        $vault->delete();

        return response()->json();
    }
}
