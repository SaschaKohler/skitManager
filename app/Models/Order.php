<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> origin/master

class Order extends Model
{
    use HasFactory;
<<<<<<< HEAD
=======
    use SoftDeletes;

>>>>>>> origin/master


    protected $guarded = [
        'id'
    ];

<<<<<<< HEAD
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
=======
//    public function event(): BelongsTo
//    {
//        return $this->belongsTo(Event::class, 'event_id');
//    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
>>>>>>> origin/master
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
<<<<<<< HEAD
=======


>>>>>>> origin/master
}
