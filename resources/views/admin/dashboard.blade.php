@include('admin.header');
@section('content')
<div class="main-wrapper">
    @include('admin.Sidebar');

    <div class="page-wrapper" style="min-height: 653px;">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">{{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }} DASHBOARD</h3>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="pmdashboard" action="{{ route('admin.dashboard.export') }}" method="GET">
                                <div class="mb-3 row">
                                    <div class="col-md-6">
                                        <label class="col-form-label">From Date</label>
                                        <input class="form-control" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">To Date</label>
                                        <input class="form-control" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Zone</label>
                                    <div class="col-md-10">
                                        @if(auth()->user()->role === 'SLM' || auth()->user()->role === 'FLM')
                                            <input type="text" class="form-control" value="{{ auth()->user()->zone->name ?? 'N/A' }}" readonly>
                                            <input type="hidden" name="zone_id" id="zone_id" value="{{ auth()->user()->zone_id }}">
                                        @else
                                            <select class="form-control" name="zone_id" id="zone_id">
                                                <option value=""> -- Select -- </option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Region</label>
                                    <div class="col-md-10">
                                        @if(auth()->user()->role === 'FLM')
                                            <input type="text" class="form-control" value="{{ auth()->user()->region->name ?? 'N/A' }}" readonly>
                                            <input type="hidden" name="region_id" id="region_id" value="{{ auth()->user()->region_id }}">
                                        @else
                                            <select class="form-control" name="region_id" id="region_id">
                                                <option value=""> -- Select -- </option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                @if(in_array(auth()->user()->role, ['admin', 'TLM']))
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">SLM</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="slm_id" id="slm_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach(\App\Models\User::where('role', 'SLM')->orderBy('name')->get() as $slm)
                                                <option value="{{ $slm->id }}">{{ $slm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">FLM</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="flm_id" id="flm_id">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">FLE</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="fle_id" id="fle_id">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>
                                @elseif(auth()->user()->role === 'SLM')
                                <input type="hidden" name="slm_id" id="slm_id" value="{{ auth()->user()->id }}">
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">FLM</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="flm_id" id="flm_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach(\App\Models\User::where('role', 'FLM')->where('reporting_to_id', auth()->user()->id)->orderBy('name')->get() as $flm)
                                                <option value="{{ $flm->id }}">{{ $flm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">FLE</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="fle_id" id="fle_id">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>
                                @elseif(auth()->user()->role === 'FLM')
                                <input type="hidden" name="flm_id" id="flm_id" value="{{ auth()->user()->id }}">
                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">FLE</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="fle_id" id="fle_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach(\App\Models\User::whereIn('role', ['FLE', 'sales_team'])->where('reporting_to_id', auth()->user()->id)->orderBy('name')->get() as $fle)
                                                <option value="{{ $fle->id }}">{{ $fle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2"> </label>
                                    <div class="col-md-10">
                                        <button type="button" name="submit" id="submitbtn" class="btn btn-primary">Submit</button>
                                        <button type="button" class="btn btn-secondary" onclick="exportData('excel')">Excel Report</button>
                                        <button type="button" class="btn btn-danger" onclick="exportData('pdf')">PDF Report</button>
                                    </div>
                                </div>
                            </form>

                            <div class="card mt-3">
                                <div class="card-body">
                                    <table id="adminRxTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>DATE</th>
                                                <th>PREFIX</th>
                                                <th>EMPLOYEE NAME</th>
                                                <th>FLM</th>
                                                <th>SLM</th>
                                                <th>HQ</th>
                                                <th>REGION</th>
                                                <th>ZONE</th>
                                                <th>TOTAL RX</th>
                                                <th>NOVELTREAT</th>
                                                <th>SEMATRINITY</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize DataTable
        var table = $('#adminRxTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.dashboard.data') }}",
                data: function(d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.zone_id = $('#zone_id').val();
                    d.region_id = $('#region_id').val();
                    d.slm_id = $('#slm_id').val();
                    d.flm_id = $('#flm_id').val();
                    d.fle_id = $('#fle_id').val();
                }
            },
            columns: [
                { data: 'date' },
                { data: 'prefix' },
                { data: 'name' },
                { data: 'flm_name' },
                { data: 'slm_name' },
                { data: 'hq' },
                { data: 'region' },
                { data: 'zone' },
                { data: 'rx_count', className: 'font-weight-bold' },
                { data: 'noveltreat_count' },
                { data: 'sematrinity_count' }
            ]
        });

        // Submit Button Click
        $('#submitbtn').on('click', function() {
            table.ajax.reload();
        });

        // Dependent Dropdowns
        function updateRegions(zoneId) {
            $('#region_id').html('<option value=""> -- Select -- </option>');
            if (zoneId) {
                $.ajax({
                    url: '/get-regions?zone_id=' + zoneId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(region) {
                            $('#region_id').append('<option value="' + region.id + '">' + region.name + '</option>');
                        });
                        @if(in_array(auth()->user()->role, ['admin', 'TLM']))
                        updateSlms(zoneId, null);
                        @endif
                    }
                });
            }
        }

        function updateSlms(zoneId, regionId) {
            var url = '/get-slms?';
            if(zoneId) url += 'zone_id=' + zoneId + '&';
            if(regionId) url += 'region_id=' + regionId;
            $('#slm_id').html('<option value=""> -- Select -- </option>');
            $('#flm_id').html('<option value=""> -- Select -- </option>');
            $('#fle_id').html('<option value=""> -- Select -- </option>');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    data.forEach(function(slm) {
                        $('#slm_id').append('<option value="' + slm.id + '">' + slm.name + '</option>');
                    });
                }
            });
        }

        function updateFlms(slmId) {
            $('#flm_id').html('<option value=""> -- Select -- </option>');
            $('#fle_id').html('<option value=""> -- Select -- </option>');
            if (slmId) {
                $.ajax({
                    url: '/get-flms?slm_id=' + slmId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(flm) {
                            $('#flm_id').append('<option value="' + flm.id + '">' + flm.name + '</option>');
                        });
                    }
                });
            }
        }

        function updateFles(flmId) {
            $('#fle_id').html('<option value=""> -- Select -- </option>');
            if (flmId) {
                $.ajax({
                    url: '/get-fles?flm_id=' + flmId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(fle) {
                            $('#fle_id').append('<option value="' + fle.id + '">' + fle.name + '</option>');
                        });
                    }
                });
            }
        }

        $('#zone_id').change(function() {
            updateRegions($(this).val());
        });

        $('#region_id').change(function() {
            @if(in_array(auth()->user()->role, ['admin', 'TLM']))
            updateSlms($('#zone_id').val(), $(this).val());
            @endif
        });

        $('#slm_id').change(function() {
            updateFlms($(this).val());
        });

        $('#flm_id').change(function() {
            updateFles($(this).val());
        });
    });

    // Custom Export Functionality
    function exportData(format) {
        var formData = $('#pmdashboard').serialize();
        var url = "{{ route('admin.dashboard.export') }}?" + formData + "&format=" + format;
        window.open(url, '_blank');
    }
</script>

@include('admin.footer');