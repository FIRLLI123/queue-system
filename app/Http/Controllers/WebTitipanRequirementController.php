<?php

namespace App\Http\Controllers;

use App\Models\TitipanRequirement;
use Illuminate\Http\Request;

class WebTitipanRequirementController extends Controller
{
    public function index()
    {
        return view('admin.titipan-requirements.index');
    }

    public function list()
    {
        $requirements = TitipanRequirement::orderBy('id')->get();
        return response()->json(['requirements' => $requirements]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:titipan_requirements,name',
        ]);

        $req = TitipanRequirement::create(['name' => $request->input('name')]);
        return response()->json(['requirement' => $req], 201);
    }

    public function show($id)
    {
        return response()->json(['requirement' => TitipanRequirement::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $req = TitipanRequirement::findOrFail($id);
        $request->validate([
            'name' => "required|string|max:255|unique:titipan_requirements,name,{$id}",
        ]);

        $req->update(['name' => $request->input('name')]);
        return response()->json(['requirement' => $req]);
    }

    public function destroy($id)
    {
        TitipanRequirement::findOrFail($id)->delete();
        return response()->json(['message' => 'Kebutuhan berhasil dihapus.']);
    }
}
