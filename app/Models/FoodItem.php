<?php
// app/Models/FoodItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

    protected $table = 'food_items';
    protected $primaryKey = 'food_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'food_name',
        'description',
        'price_per_person',
        'is_available',
    ];

    protected $casts = [
        'price_per_person' => 'decimal:2',
        'is_available' => 'boolean',
    ];
}