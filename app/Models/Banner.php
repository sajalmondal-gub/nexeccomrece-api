<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TranslatableTrait;

class Banner extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'title',
        'title_bn',
        'image',
        'link',
        'status',
        'order',
    ];

    public function getTitleAttribute($value)
    {
        return $this->getTranslatedAttribute('title', $value);
    }
}
