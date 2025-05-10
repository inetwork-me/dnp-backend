<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:shipping_country_setting'])->only('index');
    }

    public function index(Request $request)
    {
        $sort_country = $request->sort_country;
        $country_queries = Country::query();
        if ($request->sort_country) {
            $country_queries->where('name', 'like', "%$sort_country%");
        }
        $countries = $country_queries->orderBy('status', 'desc')->paginate(15);

        return view('backend.countries.index', compact('countries', 'sort_country'));
    }

    public function updateStatus(Request $request)
    {
        $country = Country::findOrFail($request->id);
        $country->status = $request->status;
        if ($country->save()) {
            return 1;
        }
        return 0;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:countries,name',
            'code' => 'required|unique:countries,code'
        ]);

        $country = new Country();

        $country->name = $request->name;
        $country->code = $request->code;

        $country->save();

        flash(translate('Country has been inserted successfully'))->success();

        return back();
    }

    public function edit(Request $request, $id)
    {
        $lang  = $request->lang;
        $country  = Country::findOrFail($id);
        return view('backend.countries.edit', compact('country', 'lang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:countries,name,' . $id,
            'code' => 'required|unique:countries,code,' . $id
        ]);

        $country = Country::findOrFail($id);
        $country->name = $request->name;
        $country->code = $request->code;

        $country->save();

        flash(translate('Country has been updated successfully'))->success();
        return redirect()->route('countries.index');
    }

    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        flash(translate('Country has been deleted successfully'))->success();
        return back();
    }
}
