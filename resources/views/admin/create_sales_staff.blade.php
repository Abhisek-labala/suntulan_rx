@include('admin.header')
<div class="main-wrapper">
    @include('admin.Sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Add Sales Team</h3>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="createTeamForm">
                                @csrf
                                <div class="row">
                                    <!-- Personal Info -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Prefix</label>
                                            <select name="prefix" class="form-control">
                                                <option value="Mr.">Mr.</option>
                                                <option value="Ms.">Ms.</option>
                                                <option value="Dr.">Dr.</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Employee ID</label>
                                            <input type="text" name="employee_id" class="form-control" placeholder="Enter EMP ID (Optional)">
                                        </div>
                                    </div>

                                    <!-- Role & Designation (Role is hidden/defaulted) -->
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
                                    <button type="submit" class="btn btn-primary btn-lg px-5">Generate Credentials & Create Team</button>
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
    // Load Initial Data
    loadZones();
    loadDesignations();

    function loadZones() {
        $.get('/get-zones', function(data) {
            let html = '<option value="">-- Select Zone --</option>';
            data.forEach(zone => {
                html += `<option value="${zone.id}">${zone.name}</option>`;
            });
            $('#zone_id').html(html);
        });
    }

    function loadDesignations() {
        $.get('/get-designations', function(data) {
            let html = '<option value="">-- Select Designation --</option>';
            data.forEach(des => {
                html += `<option value="${des.id}">${des.name}</option>`;
            });
            $('#designation_id').html(html);
        });
    }

    // Dynamic Region Loading
    $('#zone_id').change(function() {
        const zoneId = $(this).val();
        $('#region_id').html('<option value="">Loading...</option>');
        $('#hq_id').html('<option value="">-- Select HQ --</option>');
        
        if(zoneId) {
            $.get('/get-regions', { zone_id: zoneId }, function(data) {
                let html = '<option value="">-- Select Region --</option>';
                data.forEach(region => {
                    html += `<option value="${region.id}">${region.name}</option>`;
                });
                $('#region_id').html(html);
            });
        }
    });

    // Dynamic HQ Loading
    $('#region_id').change(function() {
        const regionId = $(this).val();
        $('#hq_id').html('<option value="">Loading...</option>');
        
        if(regionId) {
            $.get('/get-hqs', { region_id: regionId }, function(data) {
                let html = '<option value="">-- Select HQ --</option>';
                data.forEach(hq => {
                    html += `<option value="${hq.id}">${hq.name}</option>`;
                });
                $('#hq_id').html(html);
            });
        }
    });

    // Form Submission
    $('#createTeamForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: "{{ route('sales-team.store') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Team Created Successfully!',
                        html: `
                            <div class="text-left mt-3">
                                <p class="mb-2"><strong>Username:</strong> <code>${response.username}</code></p>
                                <p class="mb-0"><strong>Password:</strong> <code>${response.password}</code></p>
                                <p class="text-danger small mt-3">* Please copy or note down these credentials.</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Done, Go to List',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('sales-team.index') }}";
                        }
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
