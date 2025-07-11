<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            //            'status_name' => 'required|unique:status,status_name,NULL,id,deleted_at,NULL',
//            'displayname' => 'required|unique:status,displayname,NULL,id,deleted_at,NULL',
            'attachments.*' => 'file|max:102400', // 102400 KB = 100 MB
        ];
    }
}
