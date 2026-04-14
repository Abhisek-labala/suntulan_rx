<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesTeamController extends Controller
{
    public function getZones()
    {
        return response()->json(\App\Models\Zone::all());
    }

    public function getRegions(\Illuminate\Http\Request $request)
    {
        $zoneId = $request->zone_id;
        return response()->json(\App\Models\Region::where('zone_id', $zoneId)->get());
    }

    public function getHqs(\Illuminate\Http\Request $request)
    {
        $regionId = $request->region_id;
        return response()->json(\App\Models\Hq::where('region_id', $regionId)->get());
    }

    public function getDesignations()
    {
        return response()->json(\App\Models\Designation::all());
    }

    public function getManagers(Request $request)
    {
        $role = $request->role;
        $managers = [];
        
        if ($role === 'FLE' || $role === 'sales_team') {
            $managers = \App\Models\User::where('role', 'FLM')->get(['id', 'name']);
        } elseif ($role === 'FLM') {
            $managers = \App\Models\User::where('role', 'SLM')->get(['id', 'name']);
        } elseif ($role === 'SLM') {
            $managers = \App\Models\User::whereIn('role', ['admin', 'TLM'])->get(['id', 'name']);
        }
        
        return response()->json($managers);
    }

    public function index()
    {
        $staff = \App\Models\User::whereIn('role', ['sales_team', 'TLM', 'SLM', 'FLM', 'FLE'])->with(['zone', 'region', 'hq', 'designation', 'reportingTo'])->get();
        return view('admin.sales_staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.create_sales_staff');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'designation_id' => 'nullable|exists:designations,id',
            'zone_id' => 'nullable|exists:zones,id',
            'region_id' => 'nullable|exists:regions,id',
            'hq_id' => 'nullable|exists:hqs,id',
            'prefix' => 'nullable',
            'employee_id' => 'nullable|unique:users',
            'role' => 'required|in:TLM,SLM,FLM,FLE,sales_team',
            'reporting_to_id' => 'nullable|exists:users,id',
        ]);

        // Auto-generate credentials
        $username = strtolower(str_replace(' ', '', $request->name)) . '_' . rand(100, 999);
        $password = \Illuminate\Support\Str::random(8);

        $userData = array_merge($validated, [
            'username' => $username,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'plain_password' => $password,
            'role' => $request->role ?? 'sales_team'
        ]);

        \App\Models\User::create($userData);

        return response()->json([
            'success' => true, 
            'message' => "Team member created successfully! Username: $username, Password: $password",
            'username' => $username,
            'password' => $password
        ]);
    }

    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('admin.edit_sales_staff', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required',
            'designation_id' => 'nullable|exists:designations,id',
            'zone_id' => 'nullable|exists:zones,id',
            'region_id' => 'nullable|exists:regions,id',
            'hq_id' => 'nullable|exists:hqs,id',
            'prefix' => 'nullable',
            'employee_id' => 'nullable|unique:users,employee_id,' . $id,
            'role' => 'required|in:TLM,SLM,FLM,FLE,sales_team',
            'reporting_to_id' => 'nullable|exists:users,id',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true, 
            'message' => "Team member updated successfully!"
        ]);
    }

    public function destroy($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Team member deleted successfully');
    }
}
