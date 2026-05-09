<?php

namespace App\Http\Controllers;

use App\Models\Trash;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TrashController extends Controller
{
    #[OA\Get(
        path: '/api/v1/trash',
        summary: 'Dapatkan daftar sampah',
        description: 'Mengambil seluruh data sampah yang telah dicatat',
        tags: ['Trash'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar sampah berhasil diambil',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Trash')
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $trashes = Trash::all();

        return response()->json($trashes);
    }

    #[OA\Get(
        path: '/api/v1/trash/{id}',
        summary: 'Dapatkan detail sampah',
        description: 'Mengambil detail sampah berdasarkan ID',
        tags: ['Trash'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID sampah',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail sampah berhasil diambil',
                content: new OA\JsonContent(ref: '#/components/schemas/Trash')
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Sampah tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Trash not found'),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $trash = Trash::find($id);

        if (! $trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        return response()->json($trash);
    }

    #[OA\Post(
        path: '/api/v1/trash',
        summary: 'Buat data sampah baru',
        description: 'Menambahkan data sampah baru ke sistem',
        tags: ['Trash'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'category', 'weight'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Botol Plastik'),
                    new OA\Property(property: 'category', type: 'string', example: 'Anorganik'),
                    new OA\Property(property: 'weight', type: 'number', format: 'float', example: 2.5),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Data sampah berhasil dibuat',
                content: new OA\JsonContent(ref: '#/components/schemas/Trash')
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
        ]
    )]
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

    #[OA\Put(
        path: '/api/v1/trash/{id}',
        summary: 'Perbarui data sampah',
        description: 'Memperbarui data sampah yang sudah ada',
        tags: ['Trash'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID sampah',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Botol Plastik'),
                    new OA\Property(property: 'category', type: 'string', example: 'Anorganik'),
                    new OA\Property(property: 'weight', type: 'number', format: 'float', example: 2.5),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data sampah berhasil diperbarui',
                content: new OA\JsonContent(ref: '#/components/schemas/Trash')
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Sampah tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Trash not found'),
                    ]
                )
            ),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/v1/trash/{id}',
        summary: 'Hapus data sampah',
        description: 'Menghapus data sampah dari sistem',
        tags: ['Trash'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID sampah',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Data sampah berhasil dihapus',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Trash deleted successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Sampah tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Trash not found'),
                    ]
                )
            ),
        ]
    )]
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
