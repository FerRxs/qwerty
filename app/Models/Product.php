<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'category_id', 'image1', 'image2', 'image3', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Accesor para la URL de la imagen 1
    public function getImage1UrlAttribute()
    {
        return $this->image1 ? asset('storage/' . $this->image1) : null;
    }

    // Accesor para la URL de la imagen 2
    public function getImage2UrlAttribute()
    {
        return $this->image2 ? asset('storage/' . $this->image2) : null;
    }

    // Accesor para la URL de la imagen 3
    public function getImage3UrlAttribute()
    {
        return $this->image3 ? asset('storage/' . $this->image3) : null;
    }
}
