<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    public function projects() { return $this->hasMany(Project::class); }
    public function posts() { return $this->hasMany(Post::class); }
}
