<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categories', 'name')
                    ->where(
                        fn($q) => $q
                            ->where('user_id', $userId)
                            ->where('type', $this->input('type', 'both'))
                            ->whereNull('deleted_at')
                    ),
            ],
            'type' => ['required', Rule::in(['income', 'expense', 'both'])],
            'color' => ['nullable', 'string', 'max:32'],
        ];
    }
}
