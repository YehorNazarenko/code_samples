<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Case
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class Case extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * Attributes that cannot be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Attributes that should be mutated to dates.
     *
     * @var array
     */

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include cases matching the id.
     *
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeId($query, $id)
    {
        return $query->where('id', '=', $id);
    }

    //-------------------------------------------------------------------------

    /**
     * Age group belongs to the case.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ageGroup()
    {
        return $this->belongsToMany(\App\Models\AgeGroup::class, 'age_group_case',
            'case_id', 'age_group_id');
    }

    //-------------------------------------------------------------------------

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\CaseCategories::class);
    }

    //-------------------------------------------------------------------------

    /**
     * CaseRoom to which this case belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caseRoom()
    {
        return $this->belongsTo(\App\Models\CaseRoom::class);
    }

    //-------------------------------------------------------------------------

    /**
     * Scopes a query to include only results with matching name.
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeName($query, $name)
    {
        return $query->where('name', 'like', '%'.$name.'%');
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include only results matching passed category id.
     *
     * @param $query
     * @param $category
     * @return mixed
     */
    public function scopeCaseCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    //-------------------------------------------------------------------------

    /**
     * Case metrics associated with the case.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function caseMetric()
    {
        return $this->belongsToMany(\App\Models\metric::class, 'case_table_names',
                'case_id', 'table_name_id');
    }

    //-------------------------------------------------------------------------

    /**
     * Cases that belongs to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cases()
    {
        return $this->belongsToMany(\App\Models\Case::class, 'solution_case',
            'case_id', 'solution_id');
    }

    public function caseScore()
    {
        return $this->hasMany(\App\Models\CaseCaseScore::class, 'case_id');
    }

}
// end of class
// end of file
