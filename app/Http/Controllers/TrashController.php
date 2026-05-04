<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrashController extends Controller
{
    private $trashDummy = [
        [
            'id' => 1,
            'category' => 'Anorganik',
            'name' => 'Botol Plastik Bekas',
        ],
        [
            'id' => 2,
            'category' => 'Organik',
            'name' => 'Berkas Bekas',
        ],
        [
            'id' => 3,
            'category' => 'Anorganik',
            'name' => 'Kemasan Makanan Bekas',
        ],
        [
            'id' => 4,
            'category' => 'Berbahaya',
            'name' => 'Baterai Bekas',
        ],
    ];

    public function index()
    {
        return response()->json($this->trashDummy);
    }

    public function show($id)
    {
        $trash = collect($this->trashDummy)->firstWhere('id', $id);

        if ($trash) {
            return response()->json($trash);
        } else {
            return response()->json(['message' => 'Trash not found'], 404);
        }
    }

    public function create(Request $request)
    {
        try{
            $request->validate([
                'category' => 'required|string',
                'name' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
        $newTrash = [
            'id' => count($this->trashDummy) + 1,
            'category' => $request->input('category'),
            'name' => $request->input('name'),
        ];

        $this->trashDummy[] = $newTrash;

        return response()->json($newTrash, 201);
    }

    public function update(Request $request, $id)
    {
        $trash = collect($this->trashDummy)->firstWhere('id', $id);

        if (!$trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        try{
            $request->validate([
                'category' => 'required|string',
                'name' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $trash['category'] = $request->input('category');
        $trash['name'] = $request->input('name');

        return response()->json($trash);
    }

    public function destroy($id)
    {
        $trash = collect($this->trashDummy)->firstWhere('id', $id);

        if (!$trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        $this->trashDummy = array_filter($this->trashDummy, function ($t) use ($id) {
            return $t['id'] != $id;
        });

        return response()->json(['message' => 'Trash deleted successfully']);
    }

    
}
