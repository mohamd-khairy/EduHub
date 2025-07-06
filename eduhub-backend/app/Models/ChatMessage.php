<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ChatMessage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public static bool $inPermission = true;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'chat_id',
        'message',
        'is_read',
        'sent_at'
    ];

    protected $with = ['sender'];

    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
