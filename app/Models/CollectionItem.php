<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id',
        'listing_id',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
