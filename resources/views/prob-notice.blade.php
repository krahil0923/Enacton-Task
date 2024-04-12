<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
$remaining_percentage = 100 - $current_probability;
$message='';

if ($remaining_percentage > 0) {
    $message = "Sum of all prizes' probability must be 100%. Currently, it's {$current_probability}%. You have yet to add {$remaining_percentage}% to the prize.";
}

?>
{{-- TODO: add Message logic here --}}
@empty(!$message)
<div class="alert alert-danger">{{ $message }}</div>
@endempty
