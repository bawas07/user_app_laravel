<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use HasFactory;

    protected $table = 'orders';
    public const UPDATED_AT = null;

    /**
     * Get the user that owns the order.
    */
    public function user(): BelongsTo
   {
        return $this->belongsTo(User::class);
    }
}
