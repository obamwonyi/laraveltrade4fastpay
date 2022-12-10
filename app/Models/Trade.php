<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;



    protected $fillable = 
    [
        'user_id', 
        'amount_btc',
        'amount_usd',
        'settlement_country', 
        'bank',
        'account_number',
        'beneficiary',
        'trade_status'
    ];


    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}
