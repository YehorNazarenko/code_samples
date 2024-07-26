<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Human
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class Human extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table associated with the model.
     *
     * @var string
     */
    protected $table = 'humans';

    /**
     * Attributes that cannot be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $fillable = [

    ];

    /**
     * Attributes that should be mutated to dates.
     *
     * @var array
     */

    //-------------------------------------------------------------------------

    /**
     * Get the place in which the human studies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(\App\Models\Place::class);
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include humans matching the firstname.
     *
     * @param $query
     * @param $firstname
     * @return mixed
     */
    public function scopeFirstname($query, $firstname)
    {
        return $query->where('first_name', 'like', '%'.$firstname.'%');
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include humans matching the lastname.
     *
     * @param $query
     * @param $lastname
     * @return mixed
     */
    public function scopeLastname($query, $lastname)
    {
        return $query->where('last_name', 'like', '%'.$lastname.'%');
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include humans of the matching grade.
     *
     * @param $query
     * @param $grade
     * @return mixed
     */
    public function scopeGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    //-------------------------------------------------------------------------

    /**
     * Cases assigned to the human.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cases()
    {
        return $this->belongsToMany(\App\Models\Cases::class, 'human_case', 'human_id', 'case_id')->withPivot('is_archived')->wherePivot('deleted_at', null);
    }

    //-------------------------------------------------------------------------

    //-------------------------------------------------------------------------
    /**
     * @return BelongsToMany
     */
    public function entities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'human_entity');
    }

    //-------------------------------------------------------------------------

    //-------------------------------------------------------------------------
    /**
     * @return HasMany
     */
    public function humanEntitys(): HasMany
    {
        return $this->hasMany(HumanEntity::class);
    }

    //-------------------------------------------------------------------------

    public function getHumanCase($sid, $pid)
    {
        return HumanCase::where('human_id', $sid)
            ->where('case_id', $pid)
            ->where('is_archived', false)
            ->with('solution')
            ->with('user')
            ->get()
            ->toArray();
    }

    //-------------------------------------------------------------------------

    public function getHumanCaseMetric($sid, $pid)
    {
        $caseMetric = HumanMetric::where('case_id', $pid)
            ->where('human_id', $sid)
            ->where('is_archived', false)
            ->with('assignedUsers')
            ->with('metric')
            ->with('timeOfDay')
            ->get()
            ->toArray();

        $arrMetricScore = [];

        foreach ($caseMetric as $key => $item) {
            $arrMetricScore[] = [
                'id' => $item['id'],
                'name' => $item['table_name']['name'],
                'type' => $item['table_name']['metric_type'],
                'users' => $item['assigned_users'],
                'points' => $this->getMetricScore($item['id']),
                'timeOfDay' => $item['time_of_day']['name'],
            ];
        }

        return $arrMetricScore;
    }

    //-------------------------------------------------------------------------

    /**
     * The comments that belongs to the human.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function commentOnMe()
    {
        return $this->belongsToMany(Comments::class, 'human_comment', 'human_id', 'comment_id');
    }

    //-------------------------------------------------------------------------

    /**
     * The tasks that belongs to the human.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany(Tasks::class, 'task_human', 'human_id', 'task_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'human_user')
            ->wherePivot('deleted_at', null);
    }
    
    public function metrics(): HasMany
    {
        return $this->hasMany(HumanMetric::class);
    }

    public function humanUsers(): HasMany
    {
        return $this->hasMany(HumanUser::class)->with('user');
    }

    public function humanCases(): HasMany
    {
        return $this->hasMany(HumanCase::class);
    }

    public function humanComments(): HasMany
    {
        return $this->hasMany(HumanComment::class);
    }

    public function humanCases(): HasMany
    {
        return $this->hasMany(HumanCase::class)->with('case');
    }

    public function smhScreeners(): HasMany
    {
        return $this->hasMany(SmhScreener::class);
    }
}
// end of class Human
// end of file
