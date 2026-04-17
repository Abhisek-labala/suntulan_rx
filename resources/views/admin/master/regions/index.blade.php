@include('admin.header')
<div class="main-wrapper">
    @include('admin.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage Regions</h3>
                    </div>
                </div>
            </div>



            <div class="row">
                <!-- Add Form at TOP -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add Region</h4>
                            <form action="{{ route('regions.store') }}" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label>Region Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Enter region name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label>Parent Zone</label>
                                            <select name="zone_id" class="form-control" required>
                                                <option value=""> -- Select Zone -- </option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Save Region</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Table at BOTTOM -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Existing Regions</h4>
                            <div class="table-responsive">
                                <table class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Region Name</th>
                                            <th>Parent Zone</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($regions as $region)
                                        <tr>
                                            <td>{{ $region->id }}</td>
                                            <td>{{ $region->name }}</td>
                                            <td>{{ $region->zone->name ?? 'N/A' }}</td>
                                            <td class="text-right">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light mr-2 edit-region" href="#" 
                                                        data-id="{{ $region->id }}" 
                                                        data-name="{{ $region->name }}"
                                                        data-zone-id="{{ $region->zone_id }}">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('regions.destroy', $region->id) }}" method="POST" style="display:inline;" data-confirm="Are you sure you want to delete this region?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm bg-danger-light">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
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

<!-- Edit Region Modal -->
<div class="modal fade" id="editRegionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Region</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRegionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Region Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label>Parent Zone</label>
                        <select name="zone_id" id="edit_zone_id" class="form-control" required>
                            <option value=""> -- Select Zone -- </option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Region</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-region').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        const zoneId = $(this).data('zone-id');
        
        $('#edit_name').val(name);
        $('#edit_zone_id').val(zoneId);
        $('#editRegionForm').attr('action', `/admin/master/regions/${id}`);
        $('#editRegionModal').modal('show');
    });
});
</script>

@include('admin.footer')
