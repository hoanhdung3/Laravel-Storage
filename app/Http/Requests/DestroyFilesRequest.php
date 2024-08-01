<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class DestroyFilesRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->getUser();

        return array_merge(parent::rules(), [
            'all' => 'nullable|bool',
            'ids.*' => Rule::exists('files', 'id')->where(function($query) {
                $query->where('created_by', Auth::id());
            })
        ]);
    }
}
