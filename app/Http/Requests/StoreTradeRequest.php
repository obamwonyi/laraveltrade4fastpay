<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount_btc' => ['required'],
            'amount_usd' => ['required'],
            'settlement_country' => ['required'], 
            'bank' => ['required'],
            'account_number' => ['required'],
            'beneficiary' => ['required'],
        ];
    }
}
