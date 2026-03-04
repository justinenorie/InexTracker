<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');

        return $this->user() !== null
            && $category
            && (string) $category->user_id === (string) $this->user()->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categories', 'name')
                    ->ignore($categoryId)
                    ->where(
                        fn ($q) => $q
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
