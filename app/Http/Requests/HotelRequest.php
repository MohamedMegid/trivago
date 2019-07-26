<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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
        return [
            'name' => 'required|min:11',
            'rating' => 'required|numeric|min:1|max:5',
            'category' => 'required',
            'image' => 'required',
            'reputation' => 'required|numeric|min:1|max:1000',
            'price' => 'required|numeric|min:1',
            'availability' => 'required|numeric|min:1',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip_code' => 'required|numeric',
            'address' => 'required',
        ];
    }
}
