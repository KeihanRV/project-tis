<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Trash;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TrashController extends Controller
{
    private function simplifyTrash(Trash $trash): array
    {
        return [
            'id' => $trash->id,
            'name' => $trash->name,
            'category' => $trash->category,
            'weight' => $trash->weight,
            'weight_unit' => $trash->weight_unit,
        ];
    }

    #[OA\Get(
        path: '/api/v2/trash',
        summary: 'Dapatkan daftar sampah ringkas',
        description: 'Mengambil daftar sampah dengan informasi yang disederhanakan',
        tags: ['Trash V2'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar sampah berhasil diambil',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Botol Plastik'),
                            new OA\Property(property: 'category', type: 'string', example: 'Anorganik'),
                            new OA\Property(property: 'weight', type: 'number', format: 'float', example: 2.5),
                            new OA\Property(property: 'weight_unit', type: 'string', example: 'kg'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
        ]
    )]
    public function index()
    {
        $trashes = Trash::all();

        return response()->json([
            'data' => $trashes->map(fn (Trash $trash) => $this->simplifyTrash($trash)),
        ]);
    }

    #[OA\Get(
        path: '/api/v2/trash/{id}',
        summary: 'Dapatkan detail sampah ringkas',
        description: 'Mengambil detail ringkas sampah berdasarkan ID',
        tags: ['Trash V2'],
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
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Botol Plastik'),
                        new OA\Property(property: 'category', type: 'string', example: 'Anorganik'),
                        new OA\Property(property: 'weight', type: 'number', format: 'float', example: 2.5),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Sampah tidak ditemukan'
            ),
        ]
    )]
    public function show($id)
    {
        $trash = Trash::find($id);

        if (! $trash) {
            return response()->json(['message' => 'Trash not found'], 404);
        }

        return response()->json(['data' => $this->simplifyTrash($trash)]);
    }

    #[OA\Post(
        path: '/api/v2/trash',
        summary: 'Buat data sampah ringkas',
        description: 'Menambahkan data sampah dan mengembalikan format ringkas',
        tags: ['Trash V2'],
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
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Botol Plastik'),
                        new OA\Property(property: 'category', type: 'string', example: 'Anorganik'),
                        new OA\Property(property: 'weight', type: 'number', format: 'float', example: 2.5),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal'
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

        return response()->json([
            'message' => 'Trash created successfully',
            'data' => $this->simplifyTrash($trash),
        ], 201);
    }
}
