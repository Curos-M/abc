<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO implement
        return [
            //
            "isbn" => 'string|numeric|bail|digits:13|unique:books,isbn|required',
            'title' => 'string|required',
            'description' => 'string|required',
            'authors' => 'array|required',
            'authors.*' => 'numeric|required|exists:authors,id|distinct',
            'published_year' => 'numeric|min:1900|max:2020|digits:4|required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}