<?php

namespace App\Http\Requests\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
            'status_name' => [
                'required',
                Rule::unique('status', 'status_name')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
            'displayname' => [
                'required',
                Rule::unique('status', 'displayname')->ignore($decryptedId)->whereNull('deleted_at'),
            ],
        ];
    }
}
