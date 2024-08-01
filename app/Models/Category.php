<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'status',
        'image',
    ];

    protected $appends  = ['image'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'media',
    ];


    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;

        // Generate a unique slug
        $slug = Str::slug($name);
        $existingCount = self::where('slug', $slug)->where('id', '!=', $this->id)->count();

        if ($existingCount > 0) {
            $slug = $slug . '-' . (self::max('id') + 1);
        }

        $this->attributes['slug'] = $slug;
    }


    public function setImageAttribute($value)
    {

        // delete old image
        if ($this->getFirstMedia('image')) {
            $this->clearMediaCollection('image');
        }

        if ($value) {
            $this->addMedia($value)->toMediaCollection('image');
        }
    }

    public function getImageAttribute()
    {
        $media = $this->getFirstMedia('image');
        return $media ? $media->getFullUrl() : null;
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function deleteImage()
    {
        $this->clearMediaCollection('image');
    }



    public static function boot()
    {
        parent::boot();

        static::deleted(function ($value) {
            $value->deleteImage();
        });
    }


}
