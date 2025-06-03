<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveAdminRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:admins,email,' . ($this->id ?? 0),
            'phone_number' => 'required|string|max:20|unique:admins,phone_number,' . ($this->id ?? 0),
            'role' => 'required|in:1,2',
            'floor' => 'nullable|numeric|exists:floors,id',
        ];
    }
}
