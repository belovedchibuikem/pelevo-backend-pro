<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'profileImage' => ['nullable', 'file', 'image', 'max:2048'],
            'subscribedCategories' => ['nullable', 'array'],
            'subscribedCategories.*' => ['string'],
            'balance' => ['nullable', 'numeric'],
        ];
    }
} 