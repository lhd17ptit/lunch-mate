<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SaveShopRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $slug = Str::slug($this->name);
        $this->merge([
            'slug' => $slug,
        ]);
    }

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug,' . ($this->id ?? 0),
            'description' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:500',
        ];
    }
}
