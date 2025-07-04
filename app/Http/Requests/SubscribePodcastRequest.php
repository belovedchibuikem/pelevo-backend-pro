<?php
// app/Http/Requests/SubscribePodcastRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribePodcastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'show_ids' => 'required|array|min:1|max:50',
            'show_ids.*' => 'required|string|regex:/^[a-zA-Z0-9]+$/'
        ];
    }

    public function messages(): array
    {
        return [
            'show_ids.required' => 'Show IDs are required',
            'show_ids.array' => 'Show IDs must be an array',
            'show_ids.max' => 'Cannot subscribe to more than 50 shows at once',
            'show_ids.*.required' => 'Each show ID is required',
            'show_ids.*.regex' => 'Invalid show ID format'
        ];
    }
}
