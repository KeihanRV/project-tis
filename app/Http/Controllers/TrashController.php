<?php

namespace App\Http\Controllers;

use App\Models\Trash;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $trashes = Trash::all();

        return response()->json($trashes);
    }

    public function show($id)
    {
        $trash = Trash::find($id);

        if (! $trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        return response()->json($trash);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
        ]);

        $trash = Trash::create($validated);

        return response()->json($trash, 201);
    }

    public function update(Request $request, $id)
    {
        $trash = Trash::find($id);

        if (! $trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'weight' => 'sometimes|required|numeric|min:0',
        ]);

        $trash->update($validated);

        return response()->json($trash);
    }

    public function destroy($id)
    {
        $trash = Trash::find($id);

        if (! $trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        $trash->delete();

        return response()->json(['message' => 'Trash deleted successfully']);
    }
}
