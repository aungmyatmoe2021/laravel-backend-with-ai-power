<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ];
    }

    public function message(){
        return [
            'title.required' => 'You need to fill title',
            'content.required' => 'You need to fill content',
            'title.max' => 'Title is only accept 255 character',
            'title.string' => 'Title is only accept string',
            'content.string' => 'Content is only accept string',
        ];
    }
}
