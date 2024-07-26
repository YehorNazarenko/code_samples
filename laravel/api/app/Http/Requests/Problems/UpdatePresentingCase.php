<?php

namespace App\Http\Requests\Cases;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePresentingCase
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class UpdatePresentingCase extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:256',
            'description' => 'nullable',
            'category' => 'required|integer|exists:case_categories,id',
            'ageGroup' => 'sometimes|exists:age_groups,name',
            'caseRoom' => 'required|exists:case_room,id',
        ];
    }
}
// end of class
// end of file
