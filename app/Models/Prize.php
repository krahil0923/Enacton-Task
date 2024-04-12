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
        // TODO: Implement nextPrize() logic here.
        // Retrieve all prizes ordered by ID
        // $prizes = Prize::orderBy('id')->get();
        $prizes = Prize::all()->toArray();
    
        // Calculate total probability
        $totalProbability = array_reduce($prizes, function($carry, $item) {
            return $carry + $item['probability'];
        }, 0);

        // Generate a random number between 0 and 1
        $randomNumber = mt_rand() / mt_getrandmax();

        // Initialize variables
        $selectedPrize = null;
        $cumulativeProbability = 0;

        // Iterate through prizes and select one based on probability
        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize['probability'] / $totalProbability;
            if ($randomNumber <= $cumulativeProbability) {
                $selectedPrize = $prize;
                break;
            }
        }

        // Update the count of the selected prize in the database
        if ($selectedPrize) {
            Prize::where('title', $selectedPrize['title'])->decrement('awarded');
        }

        return $selectedPrize;

    }
}
