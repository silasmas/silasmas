<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * ONE-TO-MANY
     * One status for several messages
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * ONE-TO-MANY
     * One user for several messages
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
