<?php

namespace App\Http\Resources;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Listing */
class ListingResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'vault_id' => $this->vault_id,

            'vault' => new VaultResource($this->whenLoaded('vault')),
        ];
    }
}
