<?php

namespace App\Http\Requests\Priority;

use Illuminate\Foundation\Http\FormRequest;

class CreatePriorityRequest extends FormRequest
{
    /**
     * Determine if the department is authorized to make this request.
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'priority_name' => 'required|unique:priority,priority_name,NULL,id,deleted_at,NULL',
            'displayname' => 'required|unique:priority,displayname,NULL,id,deleted_at,NULL',
        ];
    }
}
