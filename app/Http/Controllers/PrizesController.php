<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;


class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();
        return view('prizes.index', compact('prizes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $current_probability = floatval(Prize::sum('probability'));
        $remaining_percentage = 100 - $current_probability;

        $validationRules = [
            'title' => 'required|string|max:255',
            'probability' => 'required|numeric|min:0|max:' . $remaining_percentage,
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $prize = Prize::where('id',$id)->first();

        $current_probability = floatval(Prize::sum('probability'));
        $remaining_percentage = 100 - $current_probability;

        $validationRules = [
            'title' => 'required|string|max:255',
            'probability' => 'required|numeric|max:' . $prize->probability,
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {
        $numberOfPrizes = $request->input('number_of_prizes', 10);

        $results = [];

        for ($i = 0; $i < $numberOfPrizes; $i++) {
            $prize = Prize::nextPrize();
            if ($prize) {
                $results[$prize['title']] = isset($results[$prize['title']]) ? $results[$prize['title']] + 1 : 1;
            }
        }

        $totalPrizes = array_sum($results);
        $probabilityDistribution = [];
        foreach ($results as $prizeName => $count) {
            $probabilityDistribution[$prizeName] = $count / $totalPrizes;
        }

        foreach ($results as $prizeName => $count) {
            $prize = Prize::where('title', $prizeName)->first();
            if ($prize) {
                $prize->update(['awarded' => $prize->count + $count]);
            } else {
                Prize::create(['title' => $prizeName, 'awarded' => $count]);
            }
        }

        return to_route('prizes.index');
    }

    public function reset()
    {
        // TODO : Write logic here
        Prize::query()->update(['awarded' => 0]);
        return to_route('prizes.index');
    }
}
