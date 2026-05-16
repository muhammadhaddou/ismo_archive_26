<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Movement extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'document_id',
        'user_id',
        'action_type',
        'date_action',
        'deadline',
        'observations',
        'is_proxy',
        'proxy_name',
        'proxy_cin',
        'proxy_document_path',
        'reference_number',
        'withdrawal_type',
    ];

    protected $casts = [
        'date_action' => 'datetime',
        'deadline'    => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}