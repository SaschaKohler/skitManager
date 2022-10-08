<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;


    protected $guarded = ['id'];

    protected $casts = [
        'extendedProps' => 'json',
        'images' => 'array'
//        'start' => 'datetime:d-m-Y H:i',
//        'end' => 'datetime:d-m-Y H:i',
    ];


    public
    function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public
    function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $defaultCalendars = ['persönlich', 'Zaunbau', 'Stockfräsen', 'Gartenpflege', 'Böschungsmähen', 'Baumpflege', 'Winterdienst', 'Sonstiges'];
        $query->when($filters['q'] ?? false, fn($query, $q) => $query
            ->where('title', 'like', '%' . $q . '%')
            ->orWhereHas('client', function ($query) use ($q) {
                $query->where('fullName', 'LIKE', '%' . $q . '%');
            })


        );
        $terms = explode(',', $filters['calendars']);
        $query->when($filters['calendars'] ?? false, fn($query, $q) => $query->where(function ($query) use ($terms) {
            foreach ($terms as $term) {
                $query->orWhereJsonContains('extendedProps->calendar', $term);
            };
        })
        );
    }

    public
    function scopeStartsAfter($query, $date)
    {
        $dateis = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        return $query->where('start', '>=', $dateis);
//        return $query->where('start', '>=', Carbon::parse($date)->format('Y-m-d'));
    }

    public
    function scopeStartsBefore(Builder $query, $date): Builder
    {
        return $query->where('start', '<=', Carbon::parse($date));
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'event_user','event_id','user_id',)
            ->using(EventUser::class)
            ->withPivot(['start_at', 'end_at', 'sum']);

    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class);
    }


}
