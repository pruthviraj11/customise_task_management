<?php

namespace App\Http\Requests\ProjectStatus;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectStatusRequest extends FormRequest
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
            'project_status_name' => 'required|unique:project_status,project_status_name,NULL,id,deleted_at,NULL',
            'displayname' => 'required|unique:project_status,displayname,NULL,id,deleted_at,NULL',
        ];
    }
}
