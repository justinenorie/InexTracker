<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => [
                'required',
                'uuid',
                Rule::exists('categories', 'id')->where(
                    fn ($q) => $q
                        ->where('user_id', $userId)
                        ->whereNull('deleted_at')
                ),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'transacted_at' => ['required', 'date'],
        ];
    }
}
