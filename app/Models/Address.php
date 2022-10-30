<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function events()
    {
        return $this->morphedByMany(Event::class, 'addressable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'addressable');
    }


}
