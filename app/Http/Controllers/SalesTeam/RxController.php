<?php

namespace App\Http\Controllers\SalesTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RxController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\RxDetail::where('user_id', $user->id);

        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $rxDetails = $query->with(['zone', 'region', 'hq'])->orderBy('date', 'desc')->get();

        $subordinates = [];
        if ($user->role === 'FLM') {
            $subordinates = \App\Models\User::where('reporting_to_id', $user->id)
                ->whereIn('role', ['FLE', 'sales_team'])
                ->with(['zone', 'region', 'hq'])
                ->get();
        }

        return view('sales_team.rx_details', compact('rxDetails', 'user', 'subordinates'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'noveltreat_count' => 'required|integer|min:0',
            'sematrinity_count' => 'required|integer|min:0',
            'sc_name' => 'required|string|max:255',
            'date' => 'required|date|before_or_equal:today'
        ]);

        $rx_count = (int)$request->noveltreat_count + (int)$request->sematrinity_count;
        if ($rx_count <= 0) {
            return redirect()->back()->with('error', 'Total RX count must be greater than 0.');
        }

        // Default to logged-in user's territory
        $zone_id = $user->zone_id;
        $region_id = $user->region_id;
        $hq_id = $user->hq_id;

        // If FLM and they selected a subordinate from the dropdown, use their territory
        if ($user->role === 'FLM') {
            $subordinate = \App\Models\User::where('reporting_to_id', $user->id)
                ->where('name', $request->sc_name)
                ->first();
            if ($subordinate) {
                $zone_id = $subordinate->zone_id;
                $region_id = $subordinate->region_id;
                $hq_id = $subordinate->hq_id;
            }
        }

        // Prevention of duplicates for the same date and SC Name (case-insensitive)
        $exists = \App\Models\RxDetail::where('user_id', $user->id)
            ->where('date', $request->date)
            ->whereRaw('LOWER(sc_name) = ?', [strtolower($request->sc_name)])
            ->first();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'You have already logged an RX count for this Date and SC Name.');
        }

        \App\Models\RxDetail::create(array_merge($request->all(), [
            'user_id' => $user->id,
            'zone_id' => $zone_id,
            'region_id' => $region_id,
            'hq_id' => $hq_id,
            'rx_count' => $rx_count
        ]));

        return redirect()->back()->with('success', 'RX Detail added successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'noveltreat_count' => 'required|integer|min:0',
            'sematrinity_count' => 'required|integer|min:0',
            'sc_name' => 'required|string|max:255',
            'date' => 'required|date|before_or_equal:today'
        ]);

        $rx_count = (int)$request->noveltreat_count + (int)$request->sematrinity_count;
        if ($rx_count <= 0) {
            return redirect()->back()->with('error', 'Total RX count must be greater than 0.');
        }

        $user = auth()->user();

        // Territory update for FLM editing subordinate records
        $zone_id = $user->zone_id;
        $region_id = $user->region_id;
        $hq_id = $user->hq_id;

        if ($user->role === 'FLM') {
            $subordinate = \App\Models\User::where('reporting_to_id', $user->id)
                ->where('name', $request->sc_name)
                ->first();
            if ($subordinate) {
                $zone_id = $subordinate->zone_id;
                $region_id = $subordinate->region_id;
                $hq_id = $subordinate->hq_id;
            }
        }

        // Check for duplicate but exclude current record
        $exists = \App\Models\RxDetail::where('user_id', $user->id)
            ->where('date', $request->date)
            ->whereRaw('LOWER(sc_name) = ?', [strtolower($request->sc_name)])
            ->where('id', '!=', $id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Another record already exists for this Date and SC Name.');
        }

        $rx = \App\Models\RxDetail::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $rx->update(array_merge($request->all(), [
            'rx_count' => $rx_count,
            'zone_id' => $zone_id,
            'region_id' => $region_id,
            'hq_id' => $hq_id,
        ]));

        return redirect()->back()->with('success', 'RX Detail updated successfully');
    }

    public function destroy($id)
    {
        $rx = \App\Models\RxDetail::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $rx->delete();
        return redirect()->back()->with('success', 'RX Detail deleted successfully');
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\RxDetail::where('user_id', $user->id);

        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $rxDetails = $query->with(['zone', 'region', 'hq'])->orderBy('date', 'desc')->get();
        $format = $request->get('format', 'excel');

        $fromLabel = $request->from_date ?\Carbon\Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toLabel = $request->to_date ?\Carbon\Carbon::parse($request->to_date)->format('d-m-Y') : 'All';
        $filename = 'RX_Report_' . $fromLabel . '_to_' . $toLabel;

        if ($format === 'pdf') {
            $logoSrc = '';
            if (file_exists(public_path('uploads/logo/Suntulan_logo.png'))) {
                $logoData = base64_encode(file_get_contents(public_path('uploads/logo/Suntulan_logo.png')));
                $logoSrc = 'data:image/png;base64,' . $logoData;
            }

            $html = '<html><head>';
            $html .= '<style>body{font-family:Arial,sans-serif;font-size:12px;}';
            $html .= '.header-tbl{width:100%;border:0;margin-bottom:10px;}';
            $html .= '.logo-cell{width:70px;text-align:left;}';
            $html .= '.title-cell{text-align:center;}';
            $html .= 'h2{color:#333;text-align:center;}';
            $html .= 'p.subtitle{text-align:center;color:#666;}';
            $html .= 'table.data-tbl{width:100%;border-collapse:collapse;margin-top:16px;}';
            $html .= 'th{background:#AE3B26;color:#fff;padding:8px;text-align:left;}';
            $html .= 'td{padding:7px;border-bottom:1px solid #ddd;}';
            $html .= 'tr:nth-child(even) td{background:#f8f8f8;}';
            $html .= '.total td{font-weight:bold;background:#fef3e2;}';
            $html .= '</style></head><body>';
            
            // Layout header with logo
            $html .= '<table class="header-tbl"><tr>';
            if ($logoSrc) {
                $html .= '<td class="logo-cell"><img src="' . $logoSrc . '" height="50"></td>';
            }
            $html .= '<td class="title-cell">';
            $html .= '<h2>RX Report - ' . htmlspecialchars($user->name) . '</h2>';
            $html .= '<p class="subtitle">Period: ' . $fromLabel . ' &nbsp;to&nbsp; ' . $toLabel . '</p>';
            $html .= '</td>';
            $html .= '<td style="width:70px;"></td>'; // spacer to center title
            $html .= '</tr></table>';

            $html .= '<table class="data-tbl">';
            $html .= '<tr><th>Date</th><th>Zone</th><th>Region</th><th>HQ</th><th>Employee Name</th><th>Total RX</th><th>Noveltreat</th><th>Sematrinity</th></tr>';
            foreach ($rxDetails as $rx) {
                $html .= '<tr>';
                $html .= '<td>' . \Carbon\Carbon::parse($rx->date)->format('d-m-Y') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->zone->name ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->region->name ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->hq->name ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($rx->sc_name ?? 'N/A') . '</td>';
                $html .= '<td><strong>' . $rx->rx_count . '</strong></td>';
                $html .= '<td>' . $rx->noveltreat_count . '</td>';
                $html .= '<td>' . $rx->sematrinity_count . '</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="total"><td colspan="5" style="text-align:right;">Sub-Totals</td>';
            $html .= '<td>' . $rxDetails->sum('rx_count') . '</td>';
            $html .= '<td>' . $rxDetails->sum('noveltreat_count') . '</td>';
            $html .= '<td>' . $rxDetails->sum('sematrinity_count') . '</td></tr>';
            $html .= '</table></body></html>';

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // Excel (CSV)
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];
        $callback = function () use ($rxDetails, $user, $fromLabel, $toLabel) {
            $handle = fopen('php://output', 'w');
            // Title rows
            fputcsv($handle, ['RX Report — ' . $user->name]);
            fputcsv($handle, ['Period: ' . $fromLabel . ' to ' . $toLabel]);
            fputcsv($handle, []);
            // Header
            fputcsv($handle, ['Date', 'Zone', 'Region', 'HQ', 'Employee Name', 'Total RX', 'Noveltreat', 'Sematrinity', 'Created On', 'Last Updated']);
            foreach ($rxDetails as $rx) {
                fputcsv($handle, [
                    \Carbon\Carbon::parse($rx->date)->format('d-m-Y'),
                    $rx->zone->name ?? 'N/A',
                    $rx->region->name ?? 'N/A',
                    $rx->hq->name ?? 'N/A',
                    $rx->sc_name ?? 'N/A',
                    $rx->rx_count,
                    $rx->noveltreat_count,
                    $rx->sematrinity_count,
                    $rx->created_at->format('d-m-Y H:i:s'),
                    $rx->updated_at->format('d-m-Y H:i:s'),
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'Total Sums', $rxDetails->sum('rx_count'), $rxDetails->sum('noveltreat_count'), $rxDetails->sum('sematrinity_count')]);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
