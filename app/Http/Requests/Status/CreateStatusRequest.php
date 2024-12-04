<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;

class CreateStatusRequest extends FormRequest
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
            'status_name' => 'required|unique:status,status_name,NULL,id,deleted_at,NULL',
            'displayname' => 'required|unique:status,displayname,NULL,id,deleted_at,NULL',
        ];
    }
}
