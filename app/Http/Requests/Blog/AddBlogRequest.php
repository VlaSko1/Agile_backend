<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class AddBlogRequest extends FormRequest
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
            'title' => ['required', 'min:3', 'max:255'],
            'text' => ['required', 'min:3', 'max:65535'],
            'public' => ['boolean'],
            'category_id' => ['required'],
            'blog_img' => ['max:16777215']
        ];
    }
}
