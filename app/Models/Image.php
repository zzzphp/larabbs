<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable = ['type', 'path'];

    public function user()
    {
        $this->belongsTo(User::class);
    }

}
