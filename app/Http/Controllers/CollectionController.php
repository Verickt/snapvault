<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;

class CollectionController extends Controller
{
    public function index()
    {
        return CollectionResource::collection(Collection::all());
    }

    public function store(CollectionRequest $request)
    {
        return new CollectionResource(Collection::create($request->validated()));
    }

    public function show(Collection $collection)
    {
        return new CollectionResource($collection);
    }

    public function update(CollectionRequest $request, Collection $collection)
    {
        $collection->update($request->validated());

        return new CollectionResource($collection);
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return response()->json();
    }
}
