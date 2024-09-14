<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;

    }

    public function view(User $user, Listing $listing)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Listing $listing)
    {
        return true;
    }

    public function delete(User $user, Listing $listing)
    {
        return true;
    }

    public function restore(User $user, Listing $listing)
    {
    }

    public function forceDelete(User $user, Listing $listing)
    {
    }
}
