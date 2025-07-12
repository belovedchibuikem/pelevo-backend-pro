<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'podcast_id' => 'required|exists:podcasts,id',
            'subscribed_at' => 'nullable|date',
            'unsubscribed_at' => 'nullable|date|after:subscribed_at',
            'is_active' => 'boolean',
        ];
    }
}
