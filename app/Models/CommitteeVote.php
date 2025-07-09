<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'jtc_member_id',
        'vote',
        'comment',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'jtc_member_id');
    }
}