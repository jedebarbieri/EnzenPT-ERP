<?php

namespace App\Models\Projects;

use App\Models\ModelCamelCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * This class is used to define the budget of any project
 * 
 * @property int $id
 * @property int $status
 * @property string $name
 * @property float $gainMargin
 * @property string $projectNumber
 */
class Budget extends ModelCamelCase
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "status",
        "name",
        "gain_margin",
        "project_name",
        "project_number",
        "project_location",
        "total_power_pick"
    ];
}
