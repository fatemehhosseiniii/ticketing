<?php

namespace App\Http\Requests\Api\Tickets;

use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Ticket::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject'=>['required','string','max:110'],
            'description'=>['required','string','max:500'],
            'file_src'=>['nullable','file','mimes:jpg,pdf,png','max:1024']
        ];
    }
}
