<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')->get();

        return response()->json($reports);
    }

    public function show($id)
    {
        $report = Report::with('user')->find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report);
    }

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

    public function destroy($id)
    {
        $report = Report::find($id);

        if (! $report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }

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

    public function search(Request $request)
    {
        $query = $request->input('query');

        $reports = Report::with('user')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        return response()->json(['results' => $reports]);
    }

    public function filterByStatus(Request $request)
    {
        $status = $request->input('status');

        if (! in_array($status, ['pending', 'in_progress', 'completed', 'rejected'])) {
            return response()->json(['message' => 'Invalid status value'], 400);
        }

        $reports = Report::with('user')->where('status', $status)->get();

        return response()->json(['reports' => $reports]);
    }

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
}
