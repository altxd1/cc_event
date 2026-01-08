<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AuditLog captures administrative actions or critical events within the
 * application. This model can be used to display logs in an admin
 * dashboard or for troubleshooting.
 */
class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    /**
     * Optional user relationship (log may not always be associated with a user).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}