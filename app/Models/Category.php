<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa SoftDeletes

class Category extends Model
{
    use HasFactory; // Utiliza el trait SoftDeletes

    protected $fillable = ['name', 'description', 'status'];
}
