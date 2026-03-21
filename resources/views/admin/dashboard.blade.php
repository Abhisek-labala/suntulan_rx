@include('admin.header');
@section('content')
<div class="main-wrapper">
    @include('admin.Sidebar');

    <div class="page-wrapper" style="min-height: 653px;">
        <div class="content container-fluid">
            @include('admin.breadcum')

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
                                        <select class="form-control" name="zone_id" id="zone_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach($zones as $zone)
                                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Region</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="region_id" id="region_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">HQ</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="hq_id" id="hq_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach($hqs as $hq)
                                                <option value="{{ $hq->id }}">{{ $hq->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Designation</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="designation_id" id="designation_id">
                                            <option value=""> -- Select -- </option>
                                            @foreach($designations as $dsg)
                                                <option value="{{ $dsg->id }}">{{ $dsg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

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
                                                <th>DESIGNATION</th>
                                                <th>HQ</th>
                                                <th>REGION</th>
                                                <th>ZONE</th>
                                                <th>RX NO.</th>
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
                    d.hq_id = $('#hq_id').val();
                    d.designation_id = $('#designation_id').val();
                }
            },
            columns: [
                { data: 'date' },
                { data: 'prefix' },
                { data: 'name' },
                { data: 'designation' },
                { data: 'hq' },
                { data: 'region' },
                { data: 'zone' },
                { data: 'rx_count' }
            ]
        });

        // Submit Button Click
        $('#submitbtn').on('click', function() {
            table.ajax.reload();
        });

        // Dependent Dropdowns
        function updateRegions(zoneId) {
            $('#region_id').html('<option value=""> -- Select -- </option>');
            $('#hq_id').html('<option value=""> -- Select -- </option>');
            if (zoneId) {
                $.ajax({
                    url: '/get-regions?zone_id=' + zoneId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(region) {
                            $('#region_id').append('<option value="' + region.id + '">' + region.name + '</option>');
                        });
                    }
                });
            }
        }

        function updateHqs(regionId) {
            $('#hq_id').html('<option value=""> -- Select -- </option>');
            if (regionId) {
                $.ajax({
                    url: '/get-hqs?region_id=' + regionId,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(hq) {
                            $('#hq_id').append('<option value="' + hq.id + '">' + hq.name + '</option>');
                        });
                    }
                });
            }
        }

        $('#zone_id').change(function() {
            updateRegions($(this).val());
        });

        $('#region_id').change(function() {
            updateHqs($(this).val());
        });
    });

    // Custom Export Functionality
    function exportData(format) {
        var formData = $('#pmdashboard').serialize();
        var url = "{{ route('admin.dashboard.export') }}?" + formData + "&format=" + format;
        window.location.href = url;
    }
</script>

@include('admin.footer');