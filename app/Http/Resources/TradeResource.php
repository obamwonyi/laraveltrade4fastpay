<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return 
        [
            'id' => (string) $this->id,
            'tradeAttributes' => 
            [
                'amountBtc' =>$this->amount_btc,
                'amountUsd' =>$this->amount_usd,
                'settlementCountry'=>$this->settlement_country,
                'bank' => $this->bank, 
                'accountNumber'=>$this->account_number,
                'beneficiary' => $this->beneficiary,
                'tradeStatus' => $this->trade_status
            ],
            'userDetails' => 
            [
                'id' => (string)$this->user->id, 
                'firstName' => $this->user->first_name, 
                'lastName' => $this->user->last_name,
                'email'=> $this->user->email
            ]
        ];
    }
}
