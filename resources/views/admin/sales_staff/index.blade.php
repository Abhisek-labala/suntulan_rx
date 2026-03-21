@include('admin.header')
<div class="main-wrapper">
    @include('admin.Sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Sales Team Management</h3>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('sales-team.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Team Member
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Employee ID</th>
                                            <th>Designation</th>
                                            <th>Zone / Region / HQ</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($staff as $member)
                                        <tr>
                                            <td>{{ $member->id }}</td>
                                            <td>{{ $member->prefix }} {{ $member->name }}</td>
                                            <td>{{ $member->username }}</td>
                                            <td><code>{{ $member->plain_password }}</code></td>
                                            <td>{{ $member->employee_id }}</td>
                                            <td>{{ $member->designation->name ?? 'N/A' }}</td>
                                            <td>
                                                <small>
                                                    Z: {{ $member->zone->name ?? 'N/A' }} <br>
                                                    R: {{ $member->region->name ?? 'N/A' }} <br>
                                                    H: {{ $member->hq->name ?? 'N/A' }}
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light mr-2" href="{{ route('sales-team.edit', $member->id) }}">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('sales-team.destroy', $member->id) }}" method="POST" style="display:inline;" data-confirm="Are you sure you want to delete this team member?">
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
@include('admin.footer')
