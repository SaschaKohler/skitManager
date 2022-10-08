<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['filter'] ?? false , fn($query,$q) => $query
        ->where('is'. ucfirst($q),'=',true)
        );
    }
    public function scopeSearch($query, array $filters)
    {
        $query->when($filters['q'] ?? false , fn($query,$q) => $query
        ->where('title','like', '%' . $q . '%')
        );
    }
  public function scopeTag($query, array $filters)
    {
        $query->when($filters['tag'] ?? false , fn($query,$q) => $query
        ->whereJsonContains('tags',$q)
        );
    }

    public function assignee()
    {
        return $this->belongsTo(User::class);
    }

}
