<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::latest()->get();

        return view('admin.dashboard', compact('attendances'));
    }

    /**
     * Export attendance logs to CSV format.
     */
    public function export(): StreamedResponse
    {
        $fileName = 'attendance_summary_'.date('Y-m-d_H-i').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Header columns sa CSV file
            fputcsv($handle, ['Student Name', 'Student ID', 'Course & Section', 'Station', 'Check-In Time', 'Status']);

            // Kunin ang mga records
            $attendances = Attendance::latest()->get();

            foreach ($attendances as $log) {
                fputcsv($handle, [
                    $log->name ?? 'N/A',
                    $log->student_id ?? 'N/A',
                    $log->course ?? 'N/A',
                    $log->station ?? 'N/A',
                    $log->created_at ? $log->created_at->format('Y-m-d h:i A') : 'N/A',
                    $log->status ?? 'Active',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Dagdagan ng validation/saving logic kung kinakailangan
        return redirect()->back()->with('status', 'Check-in successful!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());

        return redirect()->back()->with('status', 'Attendance record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('status', 'Attendance record deleted successfully!');
    }
}
