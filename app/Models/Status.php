<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class Status extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * MANY-TO-ONE
     * Several users for a status
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * MANY-TO-ONE
     * Several projects for a status
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
