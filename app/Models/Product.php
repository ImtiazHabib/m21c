<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     use HasFactory;
     protected $fillable = [
           'name',
           'description',
           'sku',
           'category_id',
           'price',
           'cost',
           'stock_quantity',
           'reorder_level',
           'created_by',
    ];

     public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);

    }

    public function inventory_transaction(){
        return $this->hasMany(InventoryTransaction::class);
    }
}
