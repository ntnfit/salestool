<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{
    use HasFactory;
    protected $table = 'log_data';
    protected $fillable = [
        'LogId',
        'Status',
        'Error_code',
        'Message',
        'DocNum'
    ];
    public $timestamps = false;
}
