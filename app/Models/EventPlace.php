<?php
// app/Models/EventPlace.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPlace extends Model
{
    use HasFactory;

    protected $table = 'event_places';
    protected $primaryKey = 'place_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'place_name',
        'description',
        'capacity',
        'price',
        'is_available',
        'image_url',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];
}