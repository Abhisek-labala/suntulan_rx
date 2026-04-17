@include('admin.header')
<div class="main-wrapper">
    @include('admin.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage Designations</h3>
                    </div>
                </div>
            </div>



            <div class="row">
                <!-- Add Form at TOP -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Add New Designation</h4>
                            <form action="{{ route('designations.store') }}" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <div class="form-group mb-0">
                                            <label>Designation Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="e.g., Regional Manager" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">Save Designation</button>
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
                            <h4 class="card-title">List of Designations</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Designation Name</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($designations as $designation)
                                        <tr>
                                            <td>{{ $designation->id }}</td>
                                            <td>{{ $designation->name }}</td>
                                            <td class="text-right">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light mr-2 edit-designation" href="#" 
                                                        data-id="{{ $designation->id }}" 
                                                        data-name="{{ $designation->name }}">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('designations.destroy', $designation->id) }}" method="POST" style="display:inline;" data-confirm="Are you sure you want to delete this designation?">
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

<!-- Edit Designation Modal -->
<div class="modal fade" id="editDesignationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Designation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDesignationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Designation Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Designation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-designation').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#edit_name').val(name);
        $('#editDesignationForm').attr('action', `/admin/master/designations/${id}`);
        $('#editDesignationModal').modal('show');
    });
});
</script>

@include('admin.footer')
