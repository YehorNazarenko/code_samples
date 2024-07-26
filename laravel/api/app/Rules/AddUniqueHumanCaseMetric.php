<?php

namespace App\Rules;

use App\Models\HumanMetric;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class AddUniqueHumanCaseMetric
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class AddUniqueHumanCaseMetric implements Rule
{
    /** @var */
    protected $metric;

    /** $case */
    protected $case; 

    /** $human */
    protected $human;

    /**
     * AddUniqueHumanCaseMetric constructor
     *
     * @param int $metric
     * @param int $case
     * @param int $human
     */
    public function __construct($metric, $case, $human)
    {
        $this->metric = $metric;
        $this->case = $case;
        $this->human = $human;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $hMetric = new HumanMetric();

        $result = $hMetric
        ->join('table_name_user', 'table_name.id', '=', 'table_name_user.table_name_id')
        ->where('table_name.table_name_id', $this->metric)
        ->where('table_name_user.user_id', $value)
        ->where('table_name.case_id', $this->case)
        ->where('table_name.human_id', $this->human)
        ->get();

        return !count($result) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected team member is already tracking this metric.';
    }
}
// end of class AddUniqueHumanCaseMetric
// end of file AddUniqueHumanCaseMetric.php
