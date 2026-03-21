@include('admin.header')
<div class="main-wrapper">
    @include('admin.Sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Edit Sales Team Member</h3>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="editTeamForm">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="team_member_id" value="{{ $user->id }}">
                                <div class="row">
                                    <!-- Personal Info -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Prefix</label>
                                            <select name="prefix" class="form-control">
                                                <option value="Mr." {{ $user->prefix == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                                <option value="Ms." {{ $user->prefix == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                                <option value="Dr." {{ $user->prefix == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Employee ID</label>
                                            <input type="text" name="employee_id" class="form-control" value="{{ $user->employee_id }}">
                                        </div>
                                    </div>

                                    <!-- Designation -->
                                    <div class="col-md-12 mt-3">
                                        <div class="form-group">
                                            <label>Designation</label>
                                            <select name="designation_id" id="designation_id" class="form-control" required>
                                                <option value="">-- Select Designation --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Organizational Hierarchy -->
                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label>Zone</label>
                                            <select name="zone_id" id="zone_id" class="form-control">
                                                <option value="">-- Select Zone --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label>Region</label>
                                            <select name="region_id" id="region_id" class="form-control">
                                                <option value="">-- Select Region --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label>HQ</label>
                                            <select name="hq_id" id="hq_id" class="form-control">
                                                <option value="">-- Select HQ --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                     <button type="submit" class="btn btn-success btn-lg px-5">Update Team Member Info</button>
                                    <a href="{{ route('sales-team.index') }}" class="btn btn-secondary btn-lg px-5 ml-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const selectedDesignation = "{{ $user->designation_id }}";
    const selectedZone = "{{ $user->zone_id }}";
    const selectedRegion = "{{ $user->region_id }}";
    const selectedHQ = "{{ $user->hq_id }}";

    // Load Initial Data
    loadZones();
    loadDesignations();

    function loadZones() {
        $.get('/get-zones', function(data) {
            let html = '<option value="">-- Select Zone --</option>';
            data.forEach(zone => {
                html += `<option value="${zone.id}" ${zone.id == selectedZone ? 'selected' : ''}>${zone.name}</option>`;
            });
            $('#zone_id').html(html);
            if(selectedZone) {
                loadRegions(selectedZone, selectedRegion);
            }
        });
    }

    function loadDesignations() {
        $.get('/get-designations', function(data) {
            let html = '<option value="">-- Select Designation --</option>';
            data.forEach(des => {
                html += `<option value="${des.id}" ${des.id == selectedDesignation ? 'selected' : ''}>${des.name}</option>`;
            });
            $('#designation_id').html(html);
        });
    }

    function loadRegions(zoneId, regionId = null) {
        if(zoneId) {
            $.get('/get-regions', { zone_id: zoneId }, function(data) {
                let html = '<option value="">-- Select Region --</option>';
                data.forEach(region => {
                    html += `<option value="${region.id}" ${region.id == regionId ? 'selected' : ''}>${region.name}</option>`;
                });
                $('#region_id').html(html);
                if(regionId) {
                    loadHQs(regionId, selectedHQ);
                }
            });
        }
    }

    function loadHQs(regionId, hqId = null) {
        if(regionId) {
            $.get('/get-hqs', { region_id: regionId }, function(data) {
                let html = '<option value="">-- Select HQ --</option>';
                data.forEach(hq => {
                    html += `<option value="${hq.id}" ${hq.id == hqId ? 'selected' : ''}>${hq.name}</option>`;
                });
                $('#hq_id').html(html);
            });
        }
    }

    // Dynamic Change Handlers
    $('#zone_id').change(function() {
        loadRegions($(this).val());
        $('#hq_id').html('<option value="">-- Select HQ --</option>');
    });

    $('#region_id').change(function() {
        loadHQs($(this).val());
    });

    // Form Submission
    $('#editTeamForm').submit(function(e) {
        e.preventDefault();
        const teamMemberId = $('#team_member_id').val();
        const formData = $(this).serialize();
        
        $.ajax({
            url: `/admin/sales-team/${teamMemberId}`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Great'
                    }).then(() => {
                        window.location.href = "{{ route('sales-team.index') }}";
                    });
                }
            },
            error: function(xhr) {
                toastr.error('Error: ' + (xhr.responseJSON.message || 'Something went wrong'));
            }
        });
    });
});
</script>

@include('admin.footer')
