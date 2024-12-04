<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
            'department_name' => 'required',
            // 'email' => 'required|unique:departments,email',
            // 'first_name' => 'required',
            // 'last_name' => 'required',
            // 'password' => 'required:string|min:8'
        ];
    }
}
