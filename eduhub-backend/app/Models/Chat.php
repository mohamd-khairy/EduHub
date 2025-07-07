<?php

namespace App\Models;

use App\Models\Scopes\ActiveStudyYearScope;
use App\Models\Scopes\RoleAccessScope;
use App\Traits\HasStudyYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Chat extends Model implements Auditable
{
    use HasFactory, HasStudyYear;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'study_year_id'
    ];

    protected $appends = ['other_user'];
    protected $with = ['messages'];

    protected static function booted()
    {
        static::addGlobalScope(new RoleAccessScope);
    }

    public function getOtherUserAttribute()
    {
        return $this->sender_id == auth()->id() ? $this->receiver : $this->sender;
    }

    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    public function receiver()
    {
        return $this->morphTo(__FUNCTION__, 'receiver_type', 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function last_message()
    {
        return $this->hasOne(ChatMessage::class, 'chat_id')->latestOfMany('sent_at');
    }
}
