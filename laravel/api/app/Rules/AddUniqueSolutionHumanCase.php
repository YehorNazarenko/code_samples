<?php

namespace App\Rules;

use App\Models\HumanCase;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class AddUniqueSolutionHumanCase
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class AddUniqueSolutionHumanCase implements Rule
{
    /** @var */
    protected $solution;

    /** $case */
    protected $case;

    /** $human */
    protected $human;

    /**
     * AddUniqueSolutionHumanCase constructor
     *
     * @param int $solution
     * @param int $case
     * @param int $human
     */
    public function __construct($solution, $case, $human)
    {
        $this->solution = $solution;
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
        $spCase = new HumanCase();

        $result = $spCase
        ->join('human_solution_user', 'human_solution_user.human_solution_id', '=', 'human_solution.id')
        ->where('human_solution.solution_id', $this->solution)
        ->where('human_solution.case_id', $this->case)
        ->where('human_solution.human_id', $this->human)
        ->where('human_solution_user.user_id', $value)
        ->get();

        return !(count($result) > 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected team member is already implementing this service.';
    }
}
// end of class AddUniqueSolutionHumanCase
// end of file AddUniqueSolutionHumanCase.php
