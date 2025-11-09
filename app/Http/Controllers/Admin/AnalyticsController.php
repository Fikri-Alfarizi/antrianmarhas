<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $dailyStats = AnalyticsService::getDailyStats($today);
        $hourlyData = AnalyticsService::getHourlyDistribution($today);
        $serviceStats = AnalyticsService::getServiceStats($today);
        $loketStats = AnalyticsService::getLoketStats($today);
        $weeklyData = AnalyticsService::getWeeklyComparison();
        $topServices = AnalyticsService::getTopServices($today);
        $monthStats = AnalyticsService::getMonthStats();
        
        return view('admin.analytics.index', compact(
            'dailyStats',
            'hourlyData',
            'serviceStats',
            'loketStats',
            'weeklyData',
            'topServices',
            'monthStats'
        ));
    }

    public function getChartData(Request $request)
    {
        $type = $request->input('type', 'hourly');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        try {
            $data = match($type) {
                'hourly' => [
                    'labels' => array_map(fn($i) => "$i:00", range(0, 23)),
                    'data' => AnalyticsService::getHourlyDistribution($date),
                ],
                'weekly' => [
                    'labels' => array_map(fn($item) => $item['date'], AnalyticsService::getWeeklyComparison()),
                    'data' => array_map(fn($item) => $item['count'], AnalyticsService::getWeeklyComparison()),
                ],
                'services' => [
                    'labels' => array_map(fn($item) => $item['nama_layanan'], AnalyticsService::getServiceStats($date)->toArray()),
                    'data' => array_map(fn($item) => $item['total'], AnalyticsService::getServiceStats($date)->toArray()),
                ],
                default => [],
            };
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDetailStats(Request $request)
    {
        $type = $request->input('type', 'service');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        try {
            $stats = match($type) {
                'service' => AnalyticsService::getServiceStats($date),
                'loket' => AnalyticsService::getLoketStats($date),
                default => [],
            };
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Date range required'], 400);
        }
        
        try {
            $stats = AnalyticsService::getDateRangeStats($startDate, $endDate);
            
            // Format untuk CSV
            $csv = "Analytics Report\n";
            $csv .= "Date Range: {$stats['date_range']}\n";
            $csv .= "Total Days: {$stats['total_days']}\n";
            $csv .= "Total Antrian: {$stats['total_antrian']}\n";
            $csv .= "Completed: {$stats['total_selesai']}\n";
            $csv .= "Cancelled: {$stats['total_batal']}\n";
            $csv .= "Avg per Day: {$stats['avg_per_day']}\n";
            $csv .= "Completion Rate: {$stats['completion_rate']}%\n";
            
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="analytics-' . now()->format('Y-m-d-H-i-s') . '.csv"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
