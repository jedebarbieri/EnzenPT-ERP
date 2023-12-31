<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $prefix 
 * @property ItemCategory $parent
 * @property Collection|ItemCategory[] $children
 * @property bool $isMain
 * @property string $prefixCode
 * @property Collection|Item[] $items
 */
class ItemCategory extends Model
{
    use HasFactory, SoftDeletes;

    const PARENT_COLUMN_NAME = 'parent_id';

    protected $fillable = [
        "name",
        "prefix",
        self::PARENT_COLUMN_NAME
    ];

    public function children()
    {
        return $this->hasMany(ItemCategory::class, self::PARENT_COLUMN_NAME);
    }

    public function parent()
    {
        return $this->belongsTo(ItemCategory::class, self::PARENT_COLUMN_NAME);
    }

    /**
     * Verifica si la entidad es una categoría principal.
     *
     * @return bool
     */
    public function getIsMainAttribute()
    {
        return $this->relationLoaded('parent') && is_null($this->attributes[self::PARENT_COLUMN_NAME]);

    }

    /**
     * Recursively gets the prefix code from the parent to the most nested category.
     *
     * @return string
     */
    public function getPrefixCodeAttribute()
    {
        if (!$this->relationLoaded('parent')) {
            // If the relationship has not been loaded then let's load it
            $this->load('parent');
        }

        if ($this->getIsMainAttribute()) {
            // It is a parent category, there is nothing more to concatenate. Terminate recursion.
            return $this->prefix;
        } else {
            // This category has a parent, we continue the recursion.
            return $this->parent->getPrefixCodeAttribute() . '-' . $this->prefix;
        }
    }

    /**
     * Obtiene los ítems asociados a esta categoría.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * It returns a list of the main categories with their children list loaded
     *
     * @return ItemCategory[]
     */
    public static function getAllMainCategories()
    {
        return ItemCategory::whereNull(self::PARENT_COLUMN_NAME)->with('children')->get();
    }
}
