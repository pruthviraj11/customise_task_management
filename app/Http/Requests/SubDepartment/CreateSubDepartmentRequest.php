<?php

namespace App\Http\Requests\SubDepartment;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubDepartmentRequest extends FormRequest
{
    /**
     * Determine if the sub_department is authorized to make this request.
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
            'sub_department_name' => 'required',
            'department_id' => 'required',
            // 'email' => 'required|unique:sub_departments,email',
            // 'first_name' => 'required',
            // 'last_name' => 'required',
            // 'password' => 'required:string|min:8'
        ];
    }
}
