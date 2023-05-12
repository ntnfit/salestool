<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogImport extends Model
{
    use HasFactory;
    protected $table = 'log_import';
    protected $fillable = [
        'type',
        'userID'
    ];
}
