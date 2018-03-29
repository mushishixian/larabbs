<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable = ['path', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
