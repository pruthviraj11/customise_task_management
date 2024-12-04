<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class customInquiryCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $input = request()->all();
        $count = DB::table('inquiries')
            ->where('email', $input['email'])
            ->where('dob', $input['dob'])
            ->where('first_name', $input['first_name'])
            ->where('last_name', $input['last_name'])
            ->where('middle_name', $input['middle_name'])
            ->where('mobile_one', $input['mobile_one'])
            ->count();

        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Inquiry with this details already exists';
    }
}
