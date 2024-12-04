<?php

namespace App\Http\Requests\ProjectStatus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectStatusRequest extends FormRequest
{
    /**
     * Determine if the company is authorized to make this request.
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
        $decryptedId = decrypt($this->route('encrypted_id'));
        return [
            'project_status_name' => [
                'required',
                Rule::unique('project_status', 'project_status_name')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
            'displayname' => [
                'required',
                Rule::unique('project_status', 'displayname')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
        ];
    }
}
