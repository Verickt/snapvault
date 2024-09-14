<?php

namespace App\Http\Resources;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Collection */
class CollectionResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'token' => $this->token,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'vault_id' => $this->vault_id,
            'user_id' => $this->user_id,

            'vault' => new VaultResource($this->whenLoaded('vault')),
        ];
    }
}
