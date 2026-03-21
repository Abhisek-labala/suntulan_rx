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
        $zones = Zone::orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $hqs = Hq::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();

        return view('admin.dashboard', compact('zones', 'regions', 'hqs', 'designations'));
    }

    /**
     * Return RX details list for DataTables AJAX.
     * Columns: DATE | PREFIX | EMPLOYEE NAME | DESIGNATION | HQ | Region | Zone | Rx count
     */
    public function getDashboardData(Request $request)
    {
        $query = RxDetail::with(['user.designation', 'user.zone', 'user.region', 'user.hq', 'zone', 'region', 'hq']);

        // --- Geography filters ---
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('hq_id')) {
            $query->where('hq_id', $request->hq_id);
        }
        if ($request->filled('designation_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $data = $records->map(function ($rx) {
            return [
            'date' => Carbon::parse($rx->date)->format('d-m-Y'),
            'prefix' => $rx->user->prefix ?? '—',
            'name' => $rx->user->name ?? 'Unknown',
            'designation' => $rx->user->designation->name ?? '—',
            'hq' => $rx->hq->name ?? ($rx->user->hq->name ?? '—'),
            'region' => $rx->region->name ?? ($rx->user->region->name ?? '—'),
            'zone' => $rx->zone->name ?? ($rx->user->zone->name ?? '—'),
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
        $query = RxDetail::with(['user.designation', 'user.zone', 'user.region', 'user.hq', 'zone', 'region', 'hq']);

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('hq_id')) {
            $query->where('hq_id', $request->hq_id);
        }
        if ($request->filled('designation_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $fromLabel = $request->filled('from_date') ?Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toLabel = $request->filled('to_date') ?Carbon::parse($request->to_date)->format('d-m-Y') : 'All';
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
            $html .= '</td>';
            $html .= '<td style="width:70px;"></td>'; // spacer to center title
            $html .= '</tr></table>';

            $html .= '<table class="data-tbl">';
            $html .= '<tr><th>DATE</th><th>PREFIX</th><th>EMPLOYEE NAME</th><th>DESIGNATION</th><th>HQ</th><th>REGION</th><th>ZONE</th><th>RX NO.</th></tr>';

            $total = 0;
            foreach ($records as $rx) {
                $total += $rx->rx_count;
                $html .= '<tr>';
                $html .= '<td>' . Carbon::parse($rx->date)->format('d-m-Y') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->user->prefix ?? '—') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->user->name ?? '—') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->user->designation->name ?? '—') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->hq->name ?? ($rx->user->hq->name ?? '—')) . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->region->name ?? ($rx->user->region->name ?? '—')) . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->zone->name ?? ($rx->user->zone->name ?? '—')) . '</td>';
                $html .= '<td>' . $rx->rx_count . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="total"><td colspan="7" style="text-align:right;">Total RX Sum</td>';
            $html .= '<td>' . $total . '</td></tr>';
            $html .= '</table></body></html>';

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($records, $fromLabel, $toLabel) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Admin RX Detailed Report']);
            fputcsv($handle, ['Period: ' . $fromLabel . ' to ' . $toLabel]);
            fputcsv($handle, []);
            fputcsv($handle, ['DATE', 'PREFIX', 'EMPLOYEE NAME', 'DESIGNATION', 'HQ', 'REGION', 'ZONE', 'RX NO.']);

            $total = 0;
            foreach ($records as $rx) {
                $total += $rx->rx_count;
                fputcsv($handle, [
                    Carbon::parse($rx->date)->format('d-m-Y'),
                    $rx->user->prefix ?? '—',
                    $rx->user->name ?? '—',
                    $rx->user->designation->name ?? '—',
                    $rx->hq->name ?? ($rx->user->hq->name ?? '—'),
                    $rx->region->name ?? ($rx->user->region->name ?? '—'),
                    $rx->zone->name ?? ($rx->user->zone->name ?? '—'),
                    $rx->rx_count,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', '', '', 'Total Sum', $total]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
