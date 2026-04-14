@include('admin.header')

<div class="main-wrapper">
    @include('admin.Sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">ANALYTICS & INSIGHTS</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Analytics</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filters & Export -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <form id="analyticsFilter" action="{{ route('admin.analytics') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Zone</label>
                                @if(auth()->user()->role === 'SLM' || auth()->user()->role === 'FLM')
                                    <input type="text" class="form-control" value="{{ auth()->user()->zone->name ?? 'N/A' }}" readonly>
                                    <input type="hidden" name="zone_id" id="zone_id" value="{{ auth()->user()->zone_id }}">
                                @else
                                    <select class="form-control" name="zone_id" id="zone_id">
                                        <option value="">-- All Zones --</option>
                                        @foreach($zones as $z)
                                            <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Region</label>
                                @if(auth()->user()->role === 'FLM')
                                    <input type="text" class="form-control" value="{{ auth()->user()->region->name ?? 'N/A' }}" readonly>
                                    <input type="hidden" name="region_id" id="region_id" value="{{ auth()->user()->region_id }}">
                                @else
                                    <select class="form-control" name="region_id" id="region_id">
                                        <option value="">-- All Regions --</option>
                                        @foreach($regions as $r)
                                            <option value="{{ $r->id }}" {{ request('region_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">HQ</label>
                                <select class="form-control" name="hq_id" id="hq_id">
                                    <option value="">-- All HQs --</option>
                                    @foreach($hqs as $h)
                                        <option value="{{ $h->id }}" {{ request('hq_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex" style="gap:10px;">
                                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fa fa-filter"></i> Apply</button>
                                <button type="button" onclick="exportAnalytics()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Daily Trend Chart -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="card-title text-muted">Rx Volume Trend (Last 15 Days)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="trendChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Product Split Chart -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="card-title text-muted">Product Split (MTD)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="productChart" style="max-height: 300px;"></canvas>
                            <div class="mt-3 text-center small text-muted">
                                <strong>Noveltreat:</strong> {{ number_format($productSplit->nt ?? 0) }} | 
                                <strong>Sematrinity:</strong> {{ number_format($productSplit->st ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Zone Wise Summary (Mini Chart + Table) -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Zone Breakdown (MTD Total)</h5>
                        </div>
                        <div class="card-body">
                             <canvas id="zoneChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Snapshot Info -->
                <div class="col-md-6">
                    <div class="card h-100 bg-light border-0">
                        <div class="card-body d-flex flex-column justify-content-center text-center">
                            <h2 class="text-primary font-weight-bold mb-0">{{ array_sum($chartTrend['values']->toArray()) }}</h2>
                            <p class="text-muted">Total Rx Logged in Last 15 Days</p>
                            <hr>
                            <h4 class="text-success font-weight-bold mb-0">{{ ($productSplit->nt ?? 0) + ($productSplit->st ?? 0) }}</h4>
                            <p class="text-muted">Current Month (MTD) Cumulative</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Listings -->
            <div class="row mt-4">
                <!-- High Performers -->
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white">
                            <h4 class="card-title mb-0"><i class="fa fa-trophy"></i> High Performers (SO > 10 Rx Yesterday)</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>SO Name</th>
                                            <th>HQ</th>
                                            <th class="text-center">Yesterday</th>
                                            <th class="text-center">7 Days</th>
                                            <th class="text-center">MTD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaderboard as $so)
                                        <tr>
                                            <td>{{ $so['name'] }}</td>
                                            <td>{{ $so['hq'] }}</td>
                                            <td class="text-center"><span class="badge badge-success px-3 py-1">{{ $so['yesterday'] }}</span></td>
                                            <td class="text-center font-weight-bold">{{ $so['week'] }}</td>
                                            <td class="text-center">{{ $so['mtd'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No high performers for yesterday.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Performers -->
                <div class="col-md-12 mt-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-danger text-white">
                            <h4 class="card-title mb-0"><i class="fa fa-exclamation-circle"></i> Low Performers (SO < 5 Rx Yesterday)</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>SO Name</th>
                                            <th>HQ</th>
                                            <th class="text-center">Yesterday</th>
                                            <th class="text-center">7 Days</th>
                                            <th class="text-center">MTD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lowPerformers as $so)
                                        <tr>
                                            <td>{{ $so['name'] }}</td>
                                            <td>{{ $so['hq'] }}</td>
                                            <td class="text-center"><span class="badge badge-danger px-3 py-1">{{ $so['yesterday'] }}</span></td>
                                            <td class="text-center font-weight-bold">{{ $so['week'] }}</td>
                                            <td class="text-center">{{ $so['mtd'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">All active users above 5 Rx.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone Wise Rx Numbers -->
                <div class="col-md-12 mt-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0"><i class="fa fa-map-marker"></i> Zone wise Rx Numbers</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Zone Name</th>
                                            <th class="text-center">Previous Day Data (Yesterday)</th>
                                            <th class="text-center">1 Week Report</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($zoneTableStats as $zs)
                                        <tr>
                                            <td><strong>{{ $zs->zone->name ?? 'N/A' }}</strong></td>
                                            <td class="text-center"><span class="badge badge-info px-3 py-1">{{ $zs->yesterday_total }}</span></td>
                                            <td class="text-center font-weight-bold">{{ $zs->week_total }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No zone data found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.footer')

<script>
$(document).ready(function() {
    // Dependent Dropdowns Logic
    $('#zone_id').change(function() {
        let zid = $(this).val();
        $('#region_id').html('<option value="">-- All Regions --</option>');
        $('#hq_id').html('<option value="">-- All HQs --</option>');
        if (zid) {
            $.get('/get-regions?zone_id=' + zid, function(data) {
                data.forEach(r => $('#region_id').append('<option value="'+r.id+'">'+r.name+'</option>'));
            });
        }
    });

    $('#region_id').change(function() {
        let rid = $(this).val();
        $('#hq_id').html('<option value="">-- All HQs --</option>');
        if (rid) {
            $.get('/get-hqs?region_id=' + rid, function(data) {
                data.forEach(h => $('#hq_id').append('<option value="'+h.id+'">'+h.name+'</option>'));
            });
        }
    });

    // --- Chart Implementations ---
    // 1. Trend Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($chartTrend['labels']),
            datasets: [{
                label: 'Total Rx',
                data: @json($chartTrend['values']),
                borderColor: '#18aefa',
                backgroundColor: 'rgba(24, 174, 250, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Product Split Chart
    new Chart(document.getElementById('productChart'), {
        type: 'doughnut',
        data: {
            labels: ['Noveltreat', 'Sematrinity'],
            datasets: [{
                data: [{{ $productSplit->nt ?? 0 }}, {{ $productSplit->st ?? 0 }}],
                backgroundColor: ['#28a745', '#ffc107']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 3. Zone Chart
    new Chart(document.getElementById('zoneChart'), {
        type: 'bar',
        data: {
            labels: @json($chartZones['labels']),
            datasets: [{
                label: 'MTD Total Rx',
                data: @json($chartZones['values']),
                backgroundColor: '#17a2b8'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});

function exportAnalytics() {
    let formData = $('#analyticsFilter').serialize();
    window.location.href = "{{ route('admin.analytics.export') }}?" + formData;
}
</script>
