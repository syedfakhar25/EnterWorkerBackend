<?php

namespace App\Http\Requests\Calenderevents;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendereventRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'color' => ['required'],
            'allDay' => ['required', 'string'],
            'draggable' => ['required', 'string'],
            'resizable' => ['required'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ];
    }
}
