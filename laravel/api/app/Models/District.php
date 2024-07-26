<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class District
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class District extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table associated with the table.
     *
     * @var string
     */
    protected $table = 'districts';

    protected $fillable = [

    ];

    /**
     * Attributes that cannot be mass assigned.
     *
     * @var array
     */
//    protected $guarded = ['id'];

    /**
     * Attributes that should be mutated to dates.
     *
     * @var array
     */

    //-------------------------------------------------------------------------

    /**
     * Scope a query to only include district with matching names.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName($query, $name)
    {
        return $query->where('name', 'like', '%'.$name.'%');
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to only include districts with matching city name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCity($query, $city)
    {
        return $query->where('city', 'like', '%'.$city.'%');
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to only include districts of a country.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $country
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    //-------------------------------------------------------------------------

    /**
     * Scope a query to include districts of state.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $state
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeState($query, $state)
    {
        return $query->where('state', $state);
    }

    //-------------------------------------------------------------------------

    /**
     * Get the places of the district.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_district')
            ->wherePivot('deleted_at', null);
    }
}
