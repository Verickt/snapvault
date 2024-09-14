<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListingController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Listing::class);

        return ListingResource::collection(Listing::all());
    }

    public function store(ListingRequest $request)
    {
        $this->authorize('create', Listing::class);

        return new ListingResource(Listing::create($request->validated()));
    }

    public function show(Listing $listing)
    {
        $this->authorize('view', $listing);

        return new ListingResource($listing);
    }

    public function update(ListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $listing->update($request->validated());

        return new ListingResource($listing);
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        $listing->delete();

        return response()->json();
    }
}
