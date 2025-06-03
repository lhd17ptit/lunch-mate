<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SaveCategoryRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $slug = Str::slug($this->title);
        $this->merge([
            'slug' => $slug,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . ($this->id ?? 0),
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
