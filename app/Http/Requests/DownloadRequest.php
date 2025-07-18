<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadRequest extends FormRequest
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
            'episode_id' => 'required|exists:episodes,id',
            'file_path' => 'required|string|max:500',
            'file_name' => 'required|string|max:255',
            'file_size' => 'nullable|integer|min:0',
            'downloaded_at' => 'nullable|date',
        ];
    }
}
