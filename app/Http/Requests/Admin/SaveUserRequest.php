<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . ($this->id ?? 0),
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . ($this->id ?? 0),
            'floor' => 'required|numeric|exists:floors,id',
        ];
    }
}
