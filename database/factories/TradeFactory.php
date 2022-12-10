<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trade>
 */
class TradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {

        return [
            'user_id' => User::all()->random()->id, 
            'amount_btc' => $this->faker->randomFloat(),
            'amount_usd' =>$this->faker->randomFloat(),
            'settlement_country'=>$this->faker->country(), 
            'bank' => $this->faker->randomElement(['UBA','GTbank','StanbicIBTC','FBank']),
            'account_number'=>$this->faker->phoneNumber(),
            'beneficiary' => $this->faker->name(),
            'trade_status' => $this->faker->randomElement(["F","S"])
        ];
    }
}
