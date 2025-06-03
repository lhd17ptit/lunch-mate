<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveFoodCategoryRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $slug = Str::slug($this->name);
        $this->merge([
            'key' => $slug,
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
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('food_categories', 'key')
                    ->where('shop_id', $this->shop_id)
                    ->ignore($this->id),
            ],
            'price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'shop_id' => 'required|exists:shops,id',
        ];
    }
}
