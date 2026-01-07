<?php
// app/Models/EventDesign.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDesign extends Model
{
    use HasFactory;

    protected $table = 'event_designs';
    protected $primaryKey = 'design_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'design_name',
        'description',
        'price',
        'is_available',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];
}