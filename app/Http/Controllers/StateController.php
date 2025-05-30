<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Country;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:manage_shipping_states'])->only('index', 'edit');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_country = $request->sort_country;
        $sort_state = $request->sort_state;

        $state_queries = State::query();
        if ($request->sort_state) {
            $state_queries->where('name', 'like', "%$sort_state%");
        }
        if ($request->sort_country) {
            $state_queries->where('country_id', $request->sort_country);
        }

        $states = $state_queries->paginate(15);
        return view('backend.states.index', compact('states', 'sort_country', 'sort_state'));
    }
    
    public function store(Request $request)
    {
        $state = new State;

        $state->name        = $request->name;
        $state->country_id  = $request->country_id;

        $state->save();

        flash(translate('State has been inserted successfully'))->success();
        return back();
    }
    
    public function edit($id)
    {
        $state  = State::findOrFail($id);
        $countries = Country::where('status', 1)->get();

        return view('backend.states.edit', compact('countries', 'state'));
    }

    public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);

        $state->name        = $request->name;
        $state->country_id  = $request->country_id;

        $state->save();

        flash(translate('State has been updated successfully'))->success();
        return redirect()->route('states.index');
    }
    
    public function destroy($id)
    {
        State::destroy($id);

        flash(translate('State has been deleted successfully'))->success();
        return redirect()->route('states.index');
    }

    public function updateStatus(Request $request)
    {
        $state = State::findOrFail($request->id);
        $state->status = $request->status;
        $state->save();

        if ($state->status) {
            foreach ($state->cities as $city) {
                $city->status = 1;
                $city->save();
            }
        }

        return 1;
    }
}
