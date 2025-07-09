<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'voting_outcome',
    ];


    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function committeeVotes()
    {
        return $this->hasMany(CommitteeVote::class);
    }

    public function recommendation()
    {
        return $this->hasOne(Recommendation::class);
    }
}
