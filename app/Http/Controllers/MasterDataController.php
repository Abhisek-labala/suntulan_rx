<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // Zones
    public function zoneIndex()
    {
        $zones = \App\Models\Zone::all();
        return view('admin.master.zones.index', compact('zones'));
    }

    public function zoneStore(Request $request)
    {
        $request->validate(['name' => 'required|unique:zones|max:255']);
        \App\Models\Zone::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Zone created successfully');
    }

    // Regions
    public function regionIndex()
    {
        $regions = \App\Models\Region::with('zone')->get();
        $zones = \App\Models\Zone::all();
        return view('admin.master.regions.index', compact('regions', 'zones'));
    }

    public function regionStore(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'zone_id' => 'required|exists:zones,id'
        ]);
        \App\Models\Region::create($request->all());
        return redirect()->back()->with('success', 'Region created successfully');
    }

    // HQs
    public function hqIndex()
    {
        $hqs = \App\Models\Hq::with('region.zone')->get();
        $regions = \App\Models\Region::all();
        return view('admin.master.hqs.index', compact('hqs', 'regions'));
    }

    public function hqStore(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'region_id' => 'required|exists:regions,id'
        ]);
        \App\Models\Hq::create($request->all());
        return redirect()->back()->with('success', 'HQ created successfully');
    }

    // Designations
    public function designationIndex()
    {
        $designations = \App\Models\Designation::all();
        return view('admin.master.designations.index', compact('designations'));
    }

    public function designationStore(Request $request)
    {
        $request->validate(['name' => 'required|unique:designations|max:255']);
        \App\Models\Designation::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Designation created successfully');
    }

    // Update Operations
    public function zoneUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|max:255|unique:zones,name,' . $id]);
        \App\Models\Zone::findOrFail($id)->update(['name' => $request->name]);
        return redirect()->back()->with('success', 'Zone updated successfully');
    }

    public function regionUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'zone_id' => 'required|exists:zones,id'
        ]);
        \App\Models\Region::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Region updated successfully');
    }

    public function hqUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'region_id' => 'required|exists:regions,id'
        ]);
        \App\Models\Hq::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'HQ updated successfully');
    }

    public function designationUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|max:255|unique:designations,name,' . $id]);
        \App\Models\Designation::findOrFail($id)->update(['name' => $request->name]);
        return redirect()->back()->with('success', 'Designation updated successfully');
    }

    // Delete Operations
    public function zoneDestroy($id)
    {
        \App\Models\Zone::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Zone deleted successfully');
    }

    public function regionDestroy($id)
    {
        \App\Models\Region::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Region deleted successfully');
    }

    public function hqDestroy($id)
    {
        \App\Models\Hq::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'HQ deleted successfully');
    }

    public function designationDestroy($id)
    {
        \App\Models\Designation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Designation deleted successfully');
    }
}
