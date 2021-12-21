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
            'task_status' => 'required|integer|between:0,1',
            'employee_id' => ['required', 'numeric'],
            'title' => ['required', 'string'],
            'deadline' => ['required', 'date'],
            'color' => ['required'],
            'allDay' => ['required', 'string'],
            // 'draggable' => ['required', 'string'],
            // 'resizable' => ['required'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ];
    }
}
