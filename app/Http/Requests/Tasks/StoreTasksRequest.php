<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTasksRequest extends FormRequest
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
            'project_id' => ['required', 'numeric'],
            'employee_id' => ['required', 'numeric'],
            'title' => ['required', 'string'],
            'percentage' => ['required', 'numeric'],
            'deadline' => ['required', 'date'],
        ];
    }
}
