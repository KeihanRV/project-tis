<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Get(
        path: '/api/v1/reports',
        summary: 'Dapatkan daftar laporan',
        description: 'Mengambil seluruh laporan dengan informasi pengguna pembuat laporan',
        tags: ['Reports'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar laporan berhasil diambil',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Report')
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
        $reports = Report::with('user')->get();

        return response()->json($reports);
    }

    #[OA\Get(
        path: '/api/v1/reports/{id}',
        summary: 'Dapatkan detail laporan',
        description: 'Mengambil detail laporan berdasarkan ID',
        tags: ['Reports'],
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
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report not found'),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $report = Report::with('user')->find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report);
    }

    #[OA\Post(
        path: '/api/v1/reports',
        summary: 'Buat laporan baru',
        description: 'Membuat laporan baru tentang insiden sampah',
        tags: ['Reports'],
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
                        new OA\Property(property: 'report', ref: '#/components/schemas/Report'),
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'sometimes|in:pending,in_progress,completed,rejected',
        ]);

        $validated['user_id'] = auth('api')->id();
        $validated['status'] = $validated['status'] ?? 'pending';

        $report = Report::create($validated);

        return response()->json(['message' => 'Report created successfully', 'report' => $report], 201);
    }

    #[OA\Put(
        path: '/api/v1/reports/{id}',
        summary: 'Perbarui laporan',
        description: 'Memperbarui informasi laporan yang sudah ada',
        tags: ['Reports'],
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
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report not found'),
                    ]
                )
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
        path: '/api/v1/reports/{id}/status',
        summary: 'Perbarui status laporan',
        description: 'Mengubah status laporan ke pending, in_progress, completed, atau rejected',
        tags: ['Reports'],
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
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal - status tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
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
        path: '/api/v1/reports/{id}',
        summary: 'Hapus laporan',
        description: 'Menghapus laporan dari sistem',
        tags: ['Reports'],
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
                description: 'Token tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Laporan tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Report not found'),
                    ]
                )
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
        path: '/api/v1/reports/search',
        summary: 'Cari laporan',
        description: 'Mencari laporan berdasarkan judul atau deskripsi',
        tags: ['Reports'],
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
                        new OA\Property(
                            property: 'results',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Report')
                        ),
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
        ]
    )]
    public function search(Request $request)
    {
        $query = $request->input('query');

        $reports = Report::with('user')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        return response()->json(['results' => $reports]);
    }

    #[OA\Post(
        path: '/api/v1/reports/filter',
        summary: 'Filter laporan berdasarkan status',
        description: 'Mengambil laporan yang difilter berdasarkan status tertentu',
        tags: ['Reports'],
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
                        new OA\Property(
                            property: 'reports',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Report')
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Status tidak valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid status value'),
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
        ]
    )]
    public function filterByStatus(Request $request)
    {
        $status = $request->input('status');

        if (! in_array($status, ['pending', 'in_progress', 'completed', 'rejected'])) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        $reports = Report::with('user')->where('status', $status)->get();

        return response()->json(['reports' => $reports]);
    }
}
