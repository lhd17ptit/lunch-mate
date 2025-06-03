<?php

namespace App\Http\Requests\Client\Order;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $cart = session()->get('cart') ?? [];

        $this->merge([
            'cart' => $cart,
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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'type' => 'nullable|in:1,2',
            'image' => 'nullable|file|max:2048',
            'cart' => 'required|array',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ];
    }
}
