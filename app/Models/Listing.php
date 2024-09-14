<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_paths',
        'vault_id',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];


    public function vault()
    {
        return $this->belongsTo(Vault::class);
    }
}
