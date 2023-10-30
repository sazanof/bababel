<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'welcome' => 'required|string',
            'date' => 'required|date',
            'files.*' => 'nullable|mimes:jpg,jpeg,png,pdf,ppt,pptx,doc,docx|max:10000'
        ];
    }

    public function messages()
    {
        return [
            'name' => __('validation.meeting.name'),
            'welcome' => __('validation.meeting.description'),
            'files.*.mimes' => __('validation.meeting.files.mimes'),
            'files.*.max' => __('validation.meeting.files.')
        ];
    }
}
