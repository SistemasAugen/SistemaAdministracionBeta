<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientsRequest extends FormRequest
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
            'name'=>'required|min:5',
            'email'=>'email|required',
            'phone'=>'digits:8',
            'celphone'=>'digits:10',
            'rfc'=>'nullable|min:12|max:13',
            'address'=>'required',
            'suburb'=>'required',
            'state_id'=>'required',
            'town_id'=>'required',
            'postal_code'=>'required|digits:5'
        ];
    }
}
