<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(private WeatherService $weatherService) {}

    public function index()
    {
        $attendances = Attendance::latest()->get();

        return view('admin.dashboard', [
            'attendances' => $attendances,
            'weather' => $this->weatherService->current(),
            'activeStudentsCount' => Attendance::where('status', 'Active')->count(),
            'occupancyRate' => round((Attendance::where('status', 'Active')->count() / 30) * 100),
            'totalCheckIns' => Attendance::whereDate('created_at', now()->today())->count(),
            'securityFlags' => Attendance::where('status', 'Flagged')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'station' => 'required|string|max:100',
        ]);

        Attendance::create($validated + ['status' => 'Active']);

        return redirect()->back()->with('success', 'Student registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required|string|max:100',
            'station' => 'required|string|max:100',
            'status' => 'required|string',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->update($validated);

        return redirect()->back()->with('success', 'Attendance record updated.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }
}
