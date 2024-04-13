<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];


    public  static function nextPrize()
    {
        $prizes = Prize::all()->toArray();
    
        $totalProbability = array_reduce($prizes, function($carry, $item) {
            return $carry + $item['probability'];
        }, 0);

        $randomNumber = mt_rand() / mt_getrandmax();

        $selectedPrize = null;
        $cumulativeProbability = 0;

        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize['probability'] / $totalProbability;
            if ($randomNumber <= $cumulativeProbability) {
                $selectedPrize = $prize;
                break;
            }
        }
        if ($selectedPrize) {
            Prize::where('title', $selectedPrize['title'])->decrement('awarded');
        }

        return $selectedPrize;

    }
}
