<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vault_id',
        'user_id',
        'token',
        'is_public',
    ];

    public function vault()
    {
        return $this->belongsTo(Vault::class);
    }

    public function user()
    {
        return $this->belongsTo(\User::class);
    }
}
