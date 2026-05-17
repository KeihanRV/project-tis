<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'status', 'trash_id', 'location', 'image_path'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trash(): BelongsToMany
    {
        return $this->belongsToMany(Trash::class, 'report_trash')->withTimestamps();
    }
}
