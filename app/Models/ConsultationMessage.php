<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Admin;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ConsultationMessage extends BaseModel implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'admin_id',
        'sender_type',
        'message',
        'file_path',
        'file_type',
        'is_read',
        'message_type',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('chat-attachments')
            ->useDisk('public');
    }
} 