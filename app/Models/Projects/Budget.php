<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * This class is used to define the budget of any project
 * 
 * @property int $id Is the system identificator
 * @property BudgetDetail[] $budgetDetails This is the list of the budgetDetails related to this budget. Each one has a relationship with the item
 * @property int $status This is the status of the Budget (to be defined)
 * @property string $name An optional name for this budget
 * @property float $gainMargin The default percentage of gain for whole budget. This value can be overwrite within the line detail level
 * @property string $projectName The name of the project which this budget belongs. * This will be moved to the project entity.
 * @property string $projectNumber The number of the project which this budget belong. * This will be moved to the project entity.
 * @property string $projectLocation The location of the project which this budget belongs. * This will be moved to the project entity.
 * @property float $totalPowerPick This is the total of the maximum power that this project can provide.
 *                                 This data is used to calculate the cost of each item or category per Watt Pick ( 0.00 € / Wp)
 */
class Budget extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    protected $fillable = [
        "status",
        "name",
        "gain_margin",
        "project_name",
        "project_number",
        "project_location",
        "total_power_pick"
    ];

    /**
     * Relationship with the lines of this detail represented by BudgetDetails
     */
    public function budgetDetails() 
    {
        return $this->hasMany(BudgetDetail::class);
    }
}