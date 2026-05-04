<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $reportDummy = [
        [
            'id' => 1,
            'title' => 'Report 1',
            'description' => 'This is the content of Report 1.',
            'location' => 'Jagakarsa, Jakarta Selatan',
            'reported_at' => '2024-06-01 10:00:00',
            'reporter_name' => 'John Doe',
            'status' => 'Pending', // Status can be 'Pending', 'In Progress', or 'Resolved'
            'trash_list' => [
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
            ],
        ],
        [
            'id' => 2,
            'title' => 'Report 2',
            'description' => 'This is the content of Report 2.',
            'location' => 'Cilandak, Jakarta Selatan',
            'reported_at' => '2024-06-02 14:30:00',
            'reporter_name' => 'Jane Smith',
            'status' => 'In Progress',
            'trash_list' => [
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
            ],
        ],
    ];

    public function index()
    {
        return response()->json($this->reportDummy);
    }

    public function show($id)
    {
        $report = collect($this->reportDummy)->firstWhere('id', $id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report);
    }

    public function updateStatus(Request $request, $id)
    {
        $report = collect($this->reportDummy)->firstWhere('id', $id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $status = $request->input('status');
        if (!in_array($status, ['Pending', 'In Progress', 'Resolved'])) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        // Update the status in the dummy data (for demonstration purposes)
        foreach ($this->reportDummy as &$r) {
            if ($r['id'] == $id) {
                $r['status'] = $status;
                break;
            }
        }

        return response()->json(['message' => 'Report status updated successfully']);
    }

    public function destroy($id)
    {
        $report = collect($this->reportDummy)->firstWhere('id', $id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        // Remove the report from the dummy data (for demonstration purposes)
        $this->reportDummy = array_filter($this->reportDummy, function ($r) use ($id) {
            return $r['id'] != $id;
        });

        return response()->json(['message' => 'Report deleted successfully']);    
    }

    public function create(Request $request)
    {
        try{
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'reporter_name' => 'required|string|max:255',
                'trash_list' => 'array',
                'trash_list.*.category' => 'required|string|in:Anorganik,Organik,Berbahaya',
                'trash_list.*.name' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
        $newReport = [
            'id' => count($this->reportDummy) + 1,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'reported_at' => now()->toDateTimeString(),
            'reporter_name' => $request->input('reporter_name'),
            'status' => 'Pending',
            'trash_list' => $request->input('trash_list', []),
        ];

        // Add the new report to the dummy data (for demonstration purposes)
        $this->reportDummy[] = $newReport;

        return response()->json(['message' => 'Report created successfully', 'report' => $newReport], 201);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = collect($this->reportDummy)->filter(function ($report) use ($query) {
            return str_contains(strtolower($report['title']), strtolower($query)) ||
                   str_contains(strtolower($report['description']), strtolower($query)) ||
                   str_contains(strtolower($report['location']), strtolower($query)) ||
                   str_contains(strtolower($report['reporter_name']), strtolower($query));
        })->values()->all();

        return response()->json(['results' => $results]);
    }

    public function filterByStatus(Request $request)
    {
        $status = $request->input('status');
        if (!in_array($status, ['Pending', 'In Progress', 'Resolved'])) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        $filteredReports = collect($this->reportDummy)->filter(function ($report) use ($status) {
            return $report['status'] === $status;
        })->values()->all();

        return response()->json(['reports' => $filteredReports]);
    }

    public function updateReport(Request $request, $id)
    {
        $report = collect($this->reportDummy)->firstWhere('id', $id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        try {
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'location' => 'sometimes|required|string|max:255',
                'reporter_name' => 'sometimes|required|string|max:255',
                'trash_list' => 'sometimes|array',
                'trash_list.*.category' => 'required_with:trash_list|string|in:Anorganik,Organik,Berbahaya',
                'trash_list.*.name' => 'required_with:trash_list|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        // Update the report in the dummy data (for demonstration purposes)
        foreach ($this->reportDummy as &$r) {
            if ($r['id'] == $id) {
                if ($request->has('title')) {
                    $r['title'] = $request->input('title');
                }
                if ($request->has('description')) {
                    $r['description'] = $request->input('description');
                }
                if ($request->has('location')) {
                    $r['location'] = $request->input('location');
                }
                if ($request->has('reporter_name')) {
                    $r['reporter_name'] = $request->input('reporter_name');
                }
                if ($request->has('trash_list')) {
                    $r['trash_list'] = $request->input('trash_list');
                }
                break;
            }
        }

        return response()->json(['message' => 'Report updated successfully']);
    }
}