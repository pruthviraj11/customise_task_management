<?php

namespace App\Http\Requests\Priority;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePriorityRequest extends FormRequest
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
            'priority_name' => [
                'required',
                Rule::unique('priority', 'priority_name')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
            'displayname' => [
                'required',
                Rule::unique('priority', 'displayname')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
        ];
    }
}
