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
        $zones        = Zone::orderBy('name')->get();
        $regions      = Region::orderBy('name')->get();
        $hqs          = Hq::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();

        return view('admin.dashboard', compact('zones', 'regions', 'hqs', 'designations'));
    }

    /**
     * Return employee-centric RX summary as JSON for DataTables AJAX.
     * Columns: PREFIX | EMPLOYEE NAME | DESIGNATION | HQ | Region | Zone | Rx no.
     */
    public function getDashboardData(Request $request)
    {
        $query = User::where('role', 'sales_team')
            ->with(['zone', 'region', 'hq', 'designation']);

        // --- Staff-level filters ---
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
            $query->where('designation_id', $request->designation_id);
        }

        $staff = $query->get();

        $data = $staff->map(function ($user) use ($request) {
            // Count RX records for this staff in the date range
            $rxQuery = RxDetail::where('user_id', $user->id);

            if ($request->filled('from_date')) {
                $rxQuery->where('date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $rxQuery->where('date', '<=', $request->to_date);
            }

            $rxCount = $rxQuery->sum('rx_count');

            return [
                'prefix'      => $user->prefix      ?? '—',
                'name'        => $user->name,
                'designation' => $user->designation->name ?? '—',
                'hq'          => $user->hq->name          ?? '—',
                'region'      => $user->region->name      ?? '—',
                'zone'        => $user->zone->name        ?? '—',
                'rx_count'    => (int) $rxCount,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Export dashboard data as CSV or PDF.
     */
    public function exportDashboard(Request $request)
    {
        $query = User::where('role', 'sales_team')
            ->with(['zone', 'region', 'hq', 'designation']);

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
            $query->where('designation_id', $request->designation_id);
        }

        $staff = $query->get();

        $rows = $staff->map(function ($user) use ($request) {
            $rxQuery = RxDetail::where('user_id', $user->id);
            if ($request->filled('from_date')) {
                $rxQuery->where('date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $rxQuery->where('date', '<=', $request->to_date);
            }
            $rxCount = $rxQuery->sum('rx_count');

            return [
                'prefix'      => $user->prefix             ?? '—',
                'name'        => $user->name,
                'designation' => $user->designation->name  ?? '—',
                'hq'          => $user->hq->name           ?? '—',
                'region'      => $user->region->name       ?? '—',
                'zone'        => $user->zone->name         ?? '—',
                'rx_count'    => (int) $rxCount,
            ];
        });

        $fromLabel = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toLabel   = $request->filled('to_date')   ? Carbon::parse($request->to_date)->format('d-m-Y')   : 'All';
        $filename  = 'Admin_RX_Dashboard_' . $fromLabel . '_to_' . $toLabel;

        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $html  = '<html><head>';
            $html .= '<style>body{font-family:Arial,sans-serif;font-size:11px;}';
            $html .= 'h2{color:#333;text-align:center;}';
            $html .= 'p.sub{text-align:center;color:#666;margin-top:-10px;}';
            $html .= 'table{width:100%;border-collapse:collapse;margin-top:14px;}';
            $html .= 'th{background:#AE3B26;color:#fff;padding:7px 6px;text-align:left;}';
            $html .= 'td{padding:6px;border-bottom:1px solid #ddd;}';
            $html .= 'tr:nth-child(even) td{background:#f8f8f8;}';
            $html .= '.total td{font-weight:bold;background:#fef3e2;}';
            $html .= '</style></head><body>';
            $html .= '<h2>Admin RX Dashboard</h2>';
            $html .= '<p class="sub">Period: ' . $fromLabel . ' &nbsp;to&nbsp; ' . $toLabel . '</p>';
            $html .= '<table>';
            $html .= '<tr><th>PREFIX</th><th>EMPLOYEE NAME</th><th>DESIGNATION</th><th>HQ</th><th>REGION</th><th>ZONE</th><th>RX NO.</th></tr>';

            $total = 0;
            foreach ($rows as $row) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['prefix'])      . '</td>';
                $html .= '<td>' . htmlspecialchars($row['name'])        . '</td>';
                $html .= '<td>' . htmlspecialchars($row['designation']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['hq'])          . '</td>';
                $html .= '<td>' . htmlspecialchars($row['region'])      . '</td>';
                $html .= '<td>' . htmlspecialchars($row['zone'])        . '</td>';
                $html .= '<td>' . $row['rx_count']                      . '</td>';
                $html .= '</tr>';
                $total += $row['rx_count'];
            }

            $html .= '<tr class="total"><td colspan="6" style="text-align:right;">Total RX</td>';
            $html .= '<td>' . $total . '</td></tr>';
            $html .= '</table></body></html>';

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // CSV / Excel
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($rows, $fromLabel, $toLabel) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Admin RX Dashboard']);
            fputcsv($handle, ['Period: ' . $fromLabel . ' to ' . $toLabel]);
            fputcsv($handle, []);
            fputcsv($handle, ['PREFIX', 'EMPLOYEE NAME', 'DESIGNATION', 'HQ', 'REGION', 'ZONE', 'RX NO.']);

            $total = 0;
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['prefix'],
                    $row['name'],
                    $row['designation'],
                    $row['hq'],
                    $row['region'],
                    $row['zone'],
                    $row['rx_count'],
                ]);
                $total += $row['rx_count'];
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', '', 'Total RX', $total]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
