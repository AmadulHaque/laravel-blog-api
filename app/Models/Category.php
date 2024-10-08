<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'status',
        'image',
    ];

    // Appended attributes for model's array and JSON form
    protected $appends = ['image', 'status_label'];

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
        self::PENDING => 'Pending',
        self::INACTIVE => 'Inactive'
    ];

    // Mutator: Set the name and generate a unique slug
    /**
     * Set the category's name and slug.
     *
     * @param string $name
     * @return void
     */
    public function setNameAttribute(string $name): void
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

    // Mutator: Set the image and manage media
    /**
     * Set the category's image.
     *
     * @param string $value
     * @return void
     */
    public function setImageAttribute(string $value): void
    {
        // Delete the old image
        if ($this->getFirstMedia('image')) {
            $this->deleteImage();
        }

        // Add new image
        if ($value) {
            $this->addMedia($value)->toMediaCollection('image');
        }
    }

    // Accessor: Get the status label
    /**
     * Get the category's status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status] ?? 'Unknown';
    }

    // Accessor: Get the image URL
    /**
     * Get the category's image URL.
     *
     * @return string|null
     */
    public function getImageAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        return $media ? $media->getFullUrl() : null;
    }

    // Relationship: Category belongs to a user
    /**
     * Get the user that owns the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Category belongs to many posts
    /**
     * The posts that belong to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    // Custom method: Delete the image
    /**
     * Delete the category's image.
     *
     * @return void
     */
    public function deleteImage(): void
    {
        $this->clearMediaCollection('image');
    }

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        // Delete the image when the category is deleted
        static::deleted(function ($category) {
            $category->deleteImage();
        });
    }
}
