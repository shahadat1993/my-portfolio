<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['mail_sent' => 'boolean', 'sheet_synced' => 'boolean']; }
}
