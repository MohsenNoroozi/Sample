<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperEmailList
 */
class EmailList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    public function getRouteKeyName(): string
    {
        return "uuid";
    }

    protected $fillable = [
        'name', // Name of uploaded file.

        'user_id', // List user
    ];

    protected $hidden = [];

    protected $casts = [
        'top_domains' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($email_list) {
            $email_list->uuid = (string) Str::uuid();
        });
    }
}
