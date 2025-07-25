<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'recommendation_text',
        'status',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}