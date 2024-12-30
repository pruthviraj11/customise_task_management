<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class CreateLocationRequest extends FormRequest
{
    /**
     * Determine if the location is authorized to make this request.
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
            'location_name' => 'required',
            // 'email' => 'required|unique:departments,email',
            // 'first_name' => 'required',
            // 'last_name' => 'required',
            // 'password' => 'required:string|min:8'
        ];
    }
}
