<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\MorphToMany;
>>>>>>> origin/master

class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

<<<<<<< HEAD
    public function events()
=======
    public function events(): MorphToMany
>>>>>>> origin/master
    {
        return $this->morphedByMany(Event::class, 'addressable');
    }

<<<<<<< HEAD
    public function users()
=======
    public function users(): MorphToMany
>>>>>>> origin/master
    {
        return $this->morphedByMany(User::class, 'addressable');
    }


}
