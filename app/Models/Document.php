<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'file_path',
        'file_name',
        'mime_type',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}