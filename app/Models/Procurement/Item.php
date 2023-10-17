<?php

namespace App\Models\Procurement;

use App\Models\ModelCamelCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property float $internalCod 
 * @property float $unitPrice
 */
class Item extends ModelCamelCase
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "name",
        "internal_cod",
        "unit_price"
    ];
}
