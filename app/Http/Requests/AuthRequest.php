<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:50'],
            'login' => ['required', 'min:5', 'max:50', 'unique:users,login'],
            'password' => ['required', 'min:6', 'max:255', 'regex:/[A-Za-z0-9!@#$%^&*-_+=?]{6,}/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'birthday' => ['required', 'date_format:Y-m-d' ],
        ];
    }
}
