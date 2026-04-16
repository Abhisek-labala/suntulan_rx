<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Zone;
use App\Models\Region;
use App\Models\Hq;
use App\Models\Designation;
use App\Models\RxDetail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $zones = Zone::orderBy('name')->get();
        $regions = Region::orderBy('name');

        if ($user->role === 'SLM') {
            $regions = $regions->where('zone_id', $user->zone_id);
        } elseif ($user->role === 'FLM') {
            $regions = $regions->where('id', $user->region_id);
        }

        $regions = $regions->get();

        return view('admin.dashboard', compact('zones', 'regions'));
    }

    /**
     * Return RX details list for DataTables AJAX.
     * Columns: DATE | PREFIX | EMPLOYEE NAME | DESIGNATION | HQ | Region | Zone | Rx count
     */
    public function getDashboardData(Request $request)
    {
        $query = RxDetail::with(['user.designation', 'user.zone', 'user.region', 'user.hq', 'user.reportingTo.reportingTo', 'zone', 'region', 'hq']);

        $user = auth()->user();

        // --- Reporting Hierarchy Filter ---
        if ($user->role === 'SLM') {
            $flmIds = User::where('reporting_to_id', $user->id)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $subId = $flmIds->merge($fleIds)->push($user->id);
            $query->whereIn('user_id', $subId);
        } elseif ($user->role === 'FLM') {
            $subId = User::where('reporting_to_id', $user->id)->pluck('id')->push($user->id);
            $query->whereIn('user_id', $subId);
        }

        // --- Geography filters from request ---
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('fle_id')) {
            $query->where('user_id', $request->fle_id);
        } elseif ($request->filled('flm_id')) {
            $flmId = $request->flm_id;
            $fleIds = User::where('reporting_to_id', $flmId)->pluck('id');
            $subId = $fleIds->push($flmId);
            $query->whereIn('user_id', $subId);
        } elseif ($request->filled('slm_id')) {
            $slmId = $request->slm_id;
            $flmIds = User::where('reporting_to_id', $slmId)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $subId = $flmIds->merge($fleIds)->push($slmId);
            $query->whereIn('user_id', $subId);
        }
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $data = $records->map(function ($rx) {
            $flm = '-';
            $slm = '-';

            if ($rx->user->role === 'sales_team' || $rx->user->role === 'FLE') {
                $flm = $rx->user->reportingTo->name ?? '-';
                $slm = $rx->user->reportingTo->reportingTo->name ?? '-';
            } elseif ($rx->user->role === 'FLM') {
                $slm = $rx->user->reportingTo->name ?? '-';
            }

            return [
                'date' => Carbon::parse($rx->date)->format('d-m-Y'),
                'prefix' => $rx->user->prefix ?? '—',
                'name' => $rx->user->name ?? 'Unknown',
                'flm_name' => $flm,
                'slm_name' => $slm,
                'designation' => $rx->user->designation->name ?? '—',
                'hq' => $rx->hq->name ?? ($rx->user->hq->name ?? '—'),
                'region' => $rx->region->name ?? ($rx->user->region->name ?? '—'),
                'zone' => $rx->zone->name ?? ($rx->user->zone->name ?? '—'),
                'noveltreat_count' => (int)$rx->noveltreat_count,
                'sematrinity_count' => (int)$rx->sematrinity_count,
                'rx_count' => (int)$rx->rx_count,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Export dashboard data list as CSV or PDF.
     */
    public function exportDashboard(Request $request)
    {
        $query = RxDetail::with(['user.designation', 'user.zone', 'user.region', 'user.hq', 'user.reportingTo.reportingTo', 'zone', 'region', 'hq']);

        $user = auth()->user();

        // --- Reporting Hierarchy Filter ---
        if ($user->role === 'SLM') {
            $flmIds = User::where('reporting_to_id', $user->id)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $subId = $flmIds->merge($fleIds)->push($user->id);
            $query->whereIn('user_id', $subId);
        } elseif ($user->role === 'FLM') {
            $subId = User::where('reporting_to_id', $user->id)->pluck('id')->push($user->id);
            $query->whereIn('user_id', $subId);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('fle_id')) {
            $query->where('user_id', $request->fle_id);
        } elseif ($request->filled('flm_id')) {
            $flmId = $request->flm_id;
            $fleIds = User::where('reporting_to_id', $flmId)->pluck('id');
            $subId = $fleIds->push($flmId);
            $query->whereIn('user_id', $subId);
        } elseif ($request->filled('slm_id')) {
            $slmId = $request->slm_id;
            $flmIds = User::where('reporting_to_id', $slmId)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $subId = $flmIds->merge($fleIds)->push($slmId);
            $query->whereIn('user_id', $subId);
        }
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $fromLabel = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toLabel = $request->filled('to_date') ? Carbon::parse($request->to_date)->format('d-m-Y') : 'All';
        
        $filterStrings = [];
        if($request->filled('zone_id')) $filterStrings[] = 'Zone: ' . (\App\Models\Zone::find($request->zone_id)->name ?? 'N/A');
        if($request->filled('region_id')) $filterStrings[] = 'Region: ' . (\App\Models\Region::find($request->region_id)->name ?? 'N/A');
        if($request->filled('slm_id')) $filterStrings[] = 'SLM: ' . (\App\Models\User::find($request->slm_id)->name ?? 'N/A');
        if($request->filled('flm_id')) $filterStrings[] = 'FLM: ' . (\App\Models\User::find($request->flm_id)->name ?? 'N/A');
        if($request->filled('fle_id')) $filterStrings[] = 'FLE: ' . (\App\Models\User::find($request->fle_id)->name ?? 'N/A');
        
        $filterDescription = !empty($filterStrings) ? implode(' | ', $filterStrings) : 'All Criteria';

        $filename = 'Admin_RX_Detailed_Report_' . $fromLabel . '_to_' . $toLabel;

        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            // PDF Logo Base64 or Public Path
            $logoPath = public_path('uploads/logo/Suntulan_logo.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }

            $html = '<html><head>';
            $html .= '<style>body{font-family:Arial,sans-serif;font-size:10px;}';
            $html .= '.header-tbl{width:100%;border:0;margin-bottom:10px;}';
            $html .= '.logo-cell{width:70px;text-align:left;}';
            $html .= '.title-cell{text-align:center;}';
            $html .= 'h2{color:#333;margin:0;padding:0;}';
            $html .= 'p.sub{color:#666;margin:5px 0 0 0;}';
            $html .= 'table.data-tbl{width:100%;border-collapse:collapse;margin-top:10px;}';
            $html .= 'table.data-tbl th{background:#AE3B26;color:#fff;padding:6px 4px;text-align:left;}';
            $html .= 'table.data-tbl td{padding:5px;border-bottom:1px solid #ddd;}';
            $html .= 'tr:nth-child(even) td{background:#f8f8f8;}';
            $html .= '.total td{font-weight:bold;background:#fef3e2;}';
            $html .= '</style></head><body>';

            // Layout header with logo
            $html .= '<table class="header-tbl"><tr>';
            if ($logoBase64) {
                $html .= '<td class="logo-cell"><img src="' . $logoBase64 . '" height="50"></td>';
            }
            $html .= '<td class="title-cell">';
            $html .= '<h2>Admin RX Detailed Report</h2>';
            $html .= '<p class="sub">Period: ' . $fromLabel . ' &nbsp;to&nbsp; ' . $toLabel . '</p>';
            $html .= '<p class="sub" style="margin-top:2px;">' . htmlspecialchars($filterDescription) . '</p>';
            $html .= '</td>';
            $html .= '<td style="width:70px;"></td>'; // spacer to center title
            $html .= '</tr></table>';

            $html .= '<table class="data-tbl">';
            $html .= '<tr><th>DATE</th><th>PREFIX</th><th>EMPLOYEE NAME</th><th>FLM</th><th>SLM</th><th>REGION</th><th>ZONE</th><th>TOTAL RX</th><th>NOVELTREAT</th><th>SEMATRINITY</th><th>CREATED ON</th><th>LAST UPDATED</th></tr>';

            $total = 0;
            foreach ($records as $rx) {
                $flm = '-';
                $slm = '-';
                if ($rx->user->role === 'sales_team' || $rx->user->role === 'FLE') {
                    $flm = $rx->user->reportingTo->name ?? '-';
                    $slm = $rx->user->reportingTo->reportingTo->name ?? '-';
                } elseif ($rx->user->role === 'FLM') {
                    $slm = $rx->user->reportingTo->name ?? '-';
                }

                $total += $rx->rx_count;
                $html .= '<tr>';
                $html .= '<td>' . Carbon::parse($rx->date)->format('d-m-Y') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->user->prefix ?? '-') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->user->name ?? '-') . '</td>';
                $html .= '<td>' . htmlspecialchars($flm) . '</td>';
                $html .= '<td>' . htmlspecialchars($slm) . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->region->name ?? ($rx->user->region->name ?? '-')) . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->zone->name ?? ($rx->user->zone->name ?? '-')) . '</td>';
                $html .= '<td><strong>' . $rx->rx_count . '</strong></td>';
                $html .= '<td>' . $rx->noveltreat_count . '</td>';
                $html .= '<td>' . $rx->sematrinity_count . '</td>';
                $html .= '<td>' . $rx->created_at->format('d-m-Y H:i:s') . '</td>';
                $html .= '<td>' . $rx->updated_at->format('d-m-Y H:i:s') . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total"><td colspan="7" style="text-align:right;">Total Sums</td>';
            $html .= '<td>' . $total . '</td>';
            $html .= '<td>' . $records->sum('noveltreat_count') . '</td>';
            $html .= '<td>' . $records->sum('sematrinity_count') . '</td><td></td><td></td></tr>';
            $html .= '</table></body></html>';

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($records, $fromLabel, $toLabel, $filterDescription) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Admin RX Detailed Report']);
            fputcsv($handle, ['Period: ' . $fromLabel . ' to ' . $toLabel]);
            fputcsv($handle, ['Filters: ' . $filterDescription]);
            fputcsv($handle, []);
            fputcsv($handle, ['DATE', 'PREFIX', 'EMPLOYEE NAME', 'FLM', 'SLM', 'REGION', 'ZONE', 'TOTAL RX', 'NOVELTREAT', 'SEMATRINITY', 'CREATED ON', 'LAST UPDATED']);

            $total = 0;
            foreach ($records as $rx) {
                $flm = '-';
                $slm = '-';
                if ($rx->user->role === 'sales_team' || $rx->user->role === 'FLE') {
                    $flm = $rx->user->reportingTo->name ?? '-';
                    $slm = $rx->user->reportingTo->reportingTo->name ?? '-';
                } elseif ($rx->user->role === 'FLM') {
                    $slm = $rx->user->reportingTo->name ?? '-';
                }

                $total += $rx->rx_count;
                fputcsv($handle, [
                    Carbon::parse($rx->date)->format('d-m-Y'),
                    $rx->user->prefix ?? '-',
                    $rx->user->name ?? '-',
                    $flm,
                    $slm,
                    $rx->region->name ?? ($rx->user->region->name ?? '-'),
                    $rx->zone->name ?? ($rx->user->zone->name ?? '-'),
                    $rx->rx_count,
                    $rx->noveltreat_count,
                    $rx->sematrinity_count,
                    $rx->created_at->format('d-m-Y H:i:s'),
                    $rx->updated_at->format('d-m-Y H:i:s'),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', '', '', 'Total Sums', $total, $records->sum('noveltreat_count'), $records->sum('sematrinity_count')]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function analytics(Request $request)
    {
        $user = auth()->user();
        
        // --- Metadata for filters ---
        $zones = Zone::orderBy('name')->get();
        $regions = Region::orderBy('name');
        $hqs = Hq::orderBy('name');

        if ($user->role === 'SLM') {
            $regions = $regions->where('zone_id', $user->zone_id);
            $hqs = $hqs->whereHas('region', function($q) use ($user) { $q->where('zone_id', $user->zone_id); });
        } elseif ($user->role === 'FLM') {
            $regions = $regions->where('id', $user->region_id);
            $hqs = $hqs->where('region_id', $user->region_id);
        }
        $regions = $regions->get();
        $hqs = $hqs->get();

        // --- Data Filtering for Analytics (Reporting Hierarchy) ---
        $baseUsers = User::whereIn('role', ['FLE', 'sales_team', 'FLM', 'SLM']);
        if ($user->role === 'SLM') {
            $flmIds = User::where('reporting_to_id', $user->id)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $allSubIds = $flmIds->merge($fleIds)->push($user->id);
            $baseUsers->whereIn('id', $allSubIds);
        } elseif ($user->role === 'FLM') {
            $allSubIds = User::where('reporting_to_id', $user->id)->pluck('id')->push($user->id);
            $baseUsers->whereIn('id', $allSubIds);
        }
        
        // Filter by user selection
        if ($request->filled('zone_id')) $baseUsers->where('zone_id', $request->zone_id);
        if ($request->filled('region_id')) $baseUsers->where('region_id', $request->region_id);
        if ($request->filled('hq_id')) $baseUsers->where('hq_id', $request->hq_id);

        $soIds = $baseUsers->pluck('id');

        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $startOfWeek = Carbon::now()->subDays(7)->format('Y-m-d');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        // Aggregated sums for each user
        $stats = RxDetail::whereIn('user_id', $soIds)
            ->select('user_id')
            ->selectRaw("SUM(CASE WHEN date = ? THEN rx_count ELSE 0 END) as yesterday_total", [$yesterday])
            ->selectRaw("SUM(CASE WHEN date >= ? THEN rx_count ELSE 0 END) as week_total", [$startOfWeek])
            ->selectRaw("SUM(CASE WHEN date >= ? THEN rx_count ELSE 0 END) as mtd_total", [$startOfMonth])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $allSOs = $baseUsers->with(['hq', 'zone', 'region'])->get();
        $leaderboard = [];
        $lowPerformers = [];

        foreach ($allSOs as $so) {
            $uStats = $stats[$so->id] ?? null;
            $y = $uStats ? (int)$uStats->yesterday_total : 0;
            $w = $uStats ? (int)$uStats->week_total : 0;
            $m = $uStats ? (int)$uStats->mtd_total : 0;
            
            $data = ['name' => $so->name, 'hq' => $so->hq->name ?? 'N/A', 'yesterday' => $y, 'week' => $w, 'mtd' => $m];
            if ($y >= 10) $leaderboard[] = $data;
            if ($y < 5) $lowPerformers[] = $data;
        }
        usort($leaderboard, fn($a, $b) => $b['yesterday'] <=> $a['yesterday']);
        usort($lowPerformers, fn($a, $b) => $a['yesterday'] <=> $b['yesterday']);

        // --- Chart Data ---
        // 1. Zone Wise Bar Chart
        $zoneStatsQuery = RxDetail::select('zone_id')
            ->selectRaw("SUM(rx_count) as total")
            ->whereIn('user_id', $soIds)
            ->where('date', '>=', $startOfMonth)
            ->groupBy('zone_id');
        $zoneData = $zoneStatsQuery->with('zone')->get();
        $chartZones = ['labels' => $zoneData->pluck('zone.name'), 'values' => $zoneData->pluck('total')];

        // 2. Product Split (MTD)
        $productSplit = RxDetail::whereIn('user_id', $soIds)
            ->where('date', '>=', $startOfMonth)
            ->selectRaw("SUM(noveltreat_count) as nt, SUM(sematrinity_count) as st")
            ->first();

        // 3. Zone wise statistics (Table)
        $zoneTableQuery = RxDetail::select('zone_id')
            ->selectRaw("SUM(CASE WHEN date = ? THEN rx_count ELSE 0 END) as yesterday_total", [$yesterday])
            ->selectRaw("SUM(CASE WHEN date >= ? THEN rx_count ELSE 0 END) as week_total", [$startOfWeek])
            ->groupBy('zone_id');

        if ($user->role === 'SLM' || $user->role === 'FLM') {
            $zoneTableQuery->where('zone_id', $user->zone_id);
        }
        $zoneTableStats = $zoneTableQuery->with('zone')->get();

        // 4. Daily Trend (Last 15 days)
        $trendStart = Carbon::now()->subDays(15)->format('Y-m-d');
        $trendData = RxDetail::whereIn('user_id', $soIds)
            ->where('date', '>=', $trendStart)
            ->select('date')
            ->selectRaw("SUM(rx_count) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $chartTrend = [
            'labels' => $trendData->map(fn($t) => Carbon::parse($t->date)->format('d M')),
            'values' => $trendData->pluck('total')
        ];

        return view('admin.analytics', compact(
            'leaderboard', 'lowPerformers', 'chartZones', 'productSplit', 'chartTrend',
            'zones', 'regions', 'hqs', 'zoneTableStats'
        ));
    }

    public function exportAnalytics(Request $request)
    {
        $user = auth()->user();
        $baseUsers = User::whereIn('role', ['FLE', 'sales_team', 'FLM', 'SLM']);
        if ($user->role === 'SLM') {
            $flmIds = User::where('reporting_to_id', $user->id)->pluck('id');
            $fleIds = User::whereIn('reporting_to_id', $flmIds)->pluck('id');
            $allSubIds = $flmIds->merge($fleIds)->push($user->id);
            $baseUsers->whereIn('id', $allSubIds);
        } elseif ($user->role === 'FLM') {
            $allSubIds = User::where('reporting_to_id', $user->id)->pluck('id')->push($user->id);
            $baseUsers->whereIn('id', $allSubIds);
        }

        if ($request->filled('zone_id')) $baseUsers->where('zone_id', $request->zone_id);
        if ($request->filled('region_id')) $baseUsers->where('region_id', $request->region_id);
        if ($request->filled('hq_id')) $baseUsers->where('hq_id', $request->hq_id);

        $soIds = $baseUsers->pluck('id');
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $startOfWeek = Carbon::now()->subDays(7)->format('Y-m-d');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

        $stats = RxDetail::whereIn('user_id', $soIds)
            ->select('user_id')
            ->selectRaw("SUM(CASE WHEN date = ? THEN rx_count ELSE 0 END) as yesterday_total", [$yesterday])
            ->selectRaw("SUM(CASE WHEN date >= ? THEN rx_count ELSE 0 END) as week_total", [$startOfWeek])
            ->selectRaw("SUM(CASE WHEN date >= ? THEN rx_count ELSE 0 END) as mtd_total", [$startOfMonth])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $allSOs = $baseUsers->with(['hq', 'zone', 'region'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Analytics_Report_'.date('Ymd').'.csv"',
        ];

        $callback = function () use ($allSOs, $stats) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Analytics Performance Report']);
            fputcsv($handle, ['Generated On: ' . date('d-m-Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['SO Name', 'Zone', 'Region', 'HQ', 'Yesterday Total', 'Last 7 Days', 'MTD Total']);

            foreach ($allSOs as $so) {
                $uS = $stats[$so->id] ?? null;
                fputcsv($handle, [
                    $so->name,
                    $so->zone->name ?? 'N/A',
                    $so->region->name ?? 'N/A',
                    $so->hq->name ?? 'N/A',
                    $uS ? $uS->yesterday_total : 0,
                    $uS ? $uS->week_total : 0,
                    $uS ? $uS->mtd_total : 0,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
