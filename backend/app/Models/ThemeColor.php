<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeColor extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['is_default' => 'boolean']; }
}
