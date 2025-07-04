<?php
// app/Http/Requests/PodcastSearchRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PodcastSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'query' => 'required|string|min:1|max:255',
            'limit' => 'integer|min:1|max:50',
            'offset' => 'integer|min:0',
            'market' => 'string|size:2'
        ];
    }

    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required',
            'query.min' => 'Search query must be at least 1 character',
            'limit.max' => 'Limit cannot exceed 50',
            'market.size' => 'Market must be a 2-character country code'
        ];
    }
}