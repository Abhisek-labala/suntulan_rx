@include('admin.header')
<div class="main-wrapper">
    @include('admin.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage HQs</h3>
                    </div>
                </div>
            </div>



            <div class="row">
                <!-- Add Form at TOP -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add HQ</h4>
                            <form action="{{ route('hqs.store') }}" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label>HQ Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Enter HQ name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label>Parent Region</label>
                                            <select name="region_id" class="form-control" required>
                                                <option value=""> -- Select Region -- </option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->zone->name ?? 'N/A' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Save HQ</button>
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
                            <h4 class="card-title">Existing HQs</h4>
                            <div class="table-responsive">
                                <table class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>HQ Name</th>
                                            <th>Parent Region</th>
                                            <th>Zone</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hqs as $hq)
                                        <tr>
                                            <td>{{ $hq->id }}</td>
                                            <td>{{ $hq->name }}</td>
                                            <td>{{ $hq->region->name ?? 'N/A' }}</td>
                                            <td>{{ $hq->region->zone->name ?? 'N/A' }}</td>
                                            <td class="text-right">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light mr-2 edit-hq" href="#" 
                                                        data-id="{{ $hq->id }}" 
                                                        data-name="{{ $hq->name }}"
                                                        data-region-id="{{ $hq->region_id }}">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('hqs.destroy', $hq->id) }}" method="POST" style="display:inline;" data-confirm="Are you sure you want to delete this HQ?">
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

<!-- Edit HQ Modal -->
<div class="modal fade" id="editHQModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit HQ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editHQForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>HQ Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label>Parent Region</label>
                        <select name="region_id" id="edit_region_id" class="form-control" required>
                            <option value=""> -- Select Region -- </option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->zone->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update HQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-hq').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        const regionId = $(this).data('region-id');
        
        $('#edit_name').val(name);
        $('#edit_region_id').val(regionId);
        $('#editHQForm').attr('action', `/admin/master/hqs/${id}`);
        $('#editHQModal').modal('show');
    });
});
</script>

@include('admin.footer')
