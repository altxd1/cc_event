<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';
    protected $primaryKey = 'package_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'package_name',
        'description',
        'price',
    ];

    /**
     * Get the events that selected this package.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'package_id', 'package_id');
    }
}