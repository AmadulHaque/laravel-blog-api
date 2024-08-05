<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'short_description',
        'content',
        'tags',
        'allow_comments',
        'is_featured',
        'status',
        'thumbnail',
    ];


    // Appended attributes for model's array and JSON form
    protected $appends = ['thumbnail', 'status_label'];

    // Hidden attributes for model's array and JSON form
    protected $hidden = [
        'created_at',
        'updated_at',
        'media',
    ];



    // Status constants
    public const PUBLISHED  = 1;
    public const PENDING    = 2;
    public const INACTIVE   = 3;


    // Status labels
    /**
     * @var string[]
     */
    public static $statuses = [
        self::PUBLISHED => 'Published',
        self::PENDING   => 'Pending',
        self::INACTIVE  => 'Inactive'
    ];

    // Mutator: Set the name and generate a unique slug
    /**
     * Set the post's name and slug.
     *
     * @param string $name
     * @return void
     */
    public function setTitleAttribute(string $name): void
    {
        $this->attributes['title'] = $name;
        $this->attributes['slug'] = Str::slug($name);
    }

    // Mutator: Set the thumbnail and manage media
    /**
     * Set the post's thumbnail.
     *
     * @param  $value
     * @return void
     */
    public function setThumbnailAttribute($value): void
    {
        // Delete the old image
        if ($this->getFirstMedia('thumbnail')) {
            $this->deleteThumbnail();
        }
        // Add new image
        if ($value) {
            $this->addMedia($value)->toMediaCollection('thumbnail');
        }
    }

    // Accessor: Get the status label
    /**
     * Get the post's status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status] ?? 'Unknown';
    }

    // Accessor: Get the thumbnail URL
    /**
     * Get the post's thumbnail URL.
     *
     * @return string|null
     */
    public function getThumbnailAttribute(): ?string
    {
        $media = $this->getFirstMedia('thumbnail');
        return $media ? $media->getFullUrl() : null;
    }

    // Relationship: Post belongs to a user
    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Post belongs to many categories
    /**
     * The categories that belong to the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Custom method: Delete the thumbnail
    /**
     * Delete the post's thumbnail.
     *
     * @return void
     */
    public function deleteThumbnail(): void
    {
        $this->clearMediaCollection('thumbnail');
    }

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        // Delete the thumbnail when the post is deleted
        static::deleted(function ($post) {
            $post->deleteThumbnail();
        });
    }
}
