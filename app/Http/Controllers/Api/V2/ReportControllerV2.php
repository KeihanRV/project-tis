<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReportControllerV2 extends Controller
{
    private function simplifyReport(Report $report): array
    {
        return [
            'id' => $report->id,
            'status' => $report->status,
            'title' => $report->title,
            'reported_at' => $report->created_at?->toDateTimeString(),
            'reporter_name' => $report->user?->name,
            'description' => $report->description,
            'trash_names' => $report->trash->pluck('name')->all(),
            'image' => $report->image_path,
        ];
    }

    #[OA\Get(
        path: '/api/v2/reports',
        summary: 'Dapatkan daftar laporan ringkas',
        description: 'Mengambil laporan dengan data yang disederhanakan untuk tampilan cepat',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar laporan berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                    ]
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
        $reports = Report::with('user', 'trash')->get();

        return response()->json([
            'data' => $reports->map(fn (Report $report) => $this->simplifyReport($report)),
        ]);
    }

    #[OA\Get(
        path: '/api/v2/reports/{id}',
        summary: 'Dapatkan detail laporan ringkas',
        description: 'Mengambil detail laporan dengan data yang disederhanakan',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID laporan',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail laporan berhasil diambil',
                content: new OA\JsonContent(ref: '#/components/schemas/Report')
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan'
            ),
        ]
    )]
    public function show($id)
    {
        $report = Report::with('user', 'trash')->find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json(['data' => $this->simplifyReport($report)]);
    }

    #[OA\Post(
        path: '/api/v2/reports',
        summary: 'Buat laporan baru (ringkas)',
        description: 'Membuat laporan baru dan mengembalikan ringkasan data laporan',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'description'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Sampah Menumpuk di Taman Kota'),
                    new OA\Property(property: 'description', type: 'string', example: 'Terdapat tumpukan sampah besar di sudut taman utara'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'rejected'], example: 'pending'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Laporan berhasil dibuat',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report created successfully'),
                        new OA\Property(property: 'report', type: 'object'),
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'sometimes|in:pending,in_progress,completed,rejected',
        ]);

        $validated['user_id'] = auth('api')->id();
        $validated['status'] = $validated['status'] ?? 'pending';

        $report = Report::create($validated);
        $report->load('user', 'trash');

        return response()->json([
            'message' => 'Report created successfully',
            'data' => $this->simplifyReport($report),
        ], 201);
    }

    #[OA\Put(
        path: '/api/v2/reports/{id}',
        summary: 'Perbarui laporan (ringkas)',
        description: 'Memperbarui laporan dan mengembalikan status sederhana',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID laporan',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Sampah Menumpuk di Taman Kota'),
                    new OA\Property(property: 'description', type: 'string', example: 'Terdapat tumpukan sampah besar'),
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'rejected'], example: 'pending'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Laporan berhasil diperbarui',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report updated successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan'
            ),
        ]
    )]
    public function updateReport(Request $request, $id)
    {
        $report = Report::find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|in:pending,in_progress,completed,rejected',
        ]);

        $report->update($validated);

        return response()->json(['message' => 'Report updated successfully']);
    }

    #[OA\Put(
        path: '/api/v2/reports/{id}/status',
        summary: 'Perbarui status laporan (ringkas)',
        description: 'Mengubah status laporan tanpa mengembalikan data penuh',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID laporan',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'rejected'], example: 'completed'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status laporan berhasil diperbarui',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report status updated successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan'
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal - status tidak valid'
            ),
        ]
    )]
    public function updateStatus(Request $request, $id)
    {
        $report = Report::find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
        ]);

        $report->update($validated);

        return response()->json(['message' => 'Report status updated successfully']);
    }

    #[OA\Delete(
        path: '/api/v2/reports/{id}',
        summary: 'Hapus laporan (ringkas)',
        description: 'Menghapus laporan dari sistem dan mengembalikan pesan singkat',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID laporan',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Laporan berhasil dihapus',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report deleted successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan'
            ),
        ]
    )]
    public function destroy($id)
    {
        $report = Report::find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }

    #[OA\Post(
        path: '/api/v2/reports/search',
        summary: 'Cari laporan ringkas',
        description: 'Mencari laporan dan mengembalikan hasil dalam format yang disederhanakan',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['query'],
                properties: [
                    new OA\Property(property: 'query', type: 'string', example: 'sampah'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hasil pencarian laporan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'results', type: 'array', items: new OA\Items),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
        ]
    )]
    public function search(Request $request)
    {
        $query = $request->input('query');

        $reports = Report::with('user', 'trash')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'data' => $reports->map(fn (Report $report) => $this->simplifyReport($report)),
        ]);
    }

    #[OA\Post(
        path: '/api/v2/reports/filter',
        summary: 'Filter laporan berdasarkan status (ringkas)',
        description: 'Mengambil laporan yang difilter dengan hasil ringkas',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'in_progress', 'completed', 'rejected'], example: 'pending'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hasil filter laporan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'reports', type: 'array', items: new OA\Items),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Status tidak valid'
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
        ]
    )]
    public function filterByStatus(Request $request)
    {
        $status = $request->input('status');

        if (! in_array($status, ['pending', 'in_progress', 'completed', 'rejected'])) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        $reports = Report::with('user', 'trash')->where('status', $status)->get();

        return response()->json([
            'data' => $reports->map(fn (Report $report) => $this->simplifyReport($report)),
        ]);
    }

    #[OA\Get(
        path: '/api/v2/reports/paginated',
        summary: 'Dapatkan laporan ringkas dengan pagination',
        description: 'Mengambil laporan dengan pagination dalam format ringkas',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Jumlah data per halaman (default: 10)',
                schema: new OA\Schema(type: 'integer', example: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Laporan berhasil diambil dengan pagination',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items),
                        new OA\Property(property: 'pagination', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
        ]
    )]
    public function indexPaginated(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $reports = Report::with('user', 'trash')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($reports->items())->map(fn ($report) => $this->simplifyReport($report)),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->perPage(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'from' => $reports->firstItem(),
                'to' => $reports->lastItem(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/v2/reports/{id}/trash',
        summary: 'Dapatkan daftar nama trash dari laporan',
        description: 'Mengambil nama-nama trash yang terkait dengan laporan spesifik',
        tags: ['Reports V2'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID laporan',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar nama trash dari laporan berhasil diambil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'report_id', type: 'integer', example: 1),
                        new OA\Property(property: 'trash_names', type: 'array', items: new OA\Items(type: 'string')),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token tidak valid'
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan'
            ),
        ]
    )]
    public function trashPerReport($id)
    {
        $report = Report::with('trash')->find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json([
            'data' => [
                'report_id' => $report->id,
                'trash_names' => $report->trash->pluck('name')->all(),
            ],
        ]);
    }
}
