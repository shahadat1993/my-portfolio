<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['tags' => 'array', 'is_active' => 'boolean']; }
}
