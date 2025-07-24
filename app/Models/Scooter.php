<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use phpDocumentor\Reflection\Types\Boolean;

class Scooter extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'max_speed',
        'range',
        'features',
        'description',
        'pricing',
        'quantity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'features' => 'array',
        'description' => 'array',
        'pricing' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAvailable(): bool
    {
        // Check if the scooter is available by counting the number of orders with status 'pending' or 'paid'
        $activeOrdersCount = $this->orders()->whereIn('status', ['pending', 'paid'])->count();
        return $activeOrdersCount < $this->quantity;
    }

}
