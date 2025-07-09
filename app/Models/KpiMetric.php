<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_name',
        'value',
        'calculated_at',
    ];
}