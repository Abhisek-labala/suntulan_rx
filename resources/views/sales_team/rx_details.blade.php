@include('admin.header')
<div class="main-wrapper">
    @include('sales_team.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">RX Reporting</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">RX Details</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Add New Entry Form -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Log Prescription (RX) Count</h4>
                            <form action="{{ route('rx.store') }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label>Zone</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->zone->name ?? 'N/A' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label>Region</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->region->name ?? 'N/A' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label>HQ</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->hq->name ?? 'N/A' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label>RX Count</label>
                                            <input type="number" name="rx_count" class="form-control" placeholder="0" required min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-0">
                                            <label>Date</label>
                                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4 text-right">
                                        <button type="submit" class="btn btn-primary px-5">Submit RX Log</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RX History with Filter + Export -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">My RX History</h4>

                            <!-- Date Range Filter -->
                            <form method="GET" action="{{ route('rx.index') }}" class="mb-4">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex" style="gap:8px;">
                                        <button type="submit" class="btn btn-info px-4">
                                            <i class="fa fa-filter mr-1"></i> Filter
                                        </button>
                                        <a href="{{ route('rx.index') }}" class="btn btn-secondary px-4">
                                            <i class="fa fa-times mr-1"></i> Reset
                                        </a>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="{{ route('rx.export', array_merge(request()->only('from_date','to_date'), ['format'=>'pdf'])) }}"
                                           class="btn btn-danger px-3 mr-2" target="_blank">
                                            <i class="fa fa-file-pdf-o mr-1"></i> PDF
                                        </a>
                                        <a href="{{ route('rx.export', array_merge(request()->only('from_date','to_date'), ['format'=>'excel'])) }}"
                                           class="btn btn-success px-3" target="_blank">
                                            <i class="fa fa-file-excel-o mr-1"></i> Excel
                                        </a>
                                    </div>
                                </div>
                            </form>

                            @if(request('from_date') || request('to_date'))
                            <div class="alert alert-info mb-3 py-2">
                                Showing records
                                @if(request('from_date')) from <strong>{{ \Carbon\Carbon::parse(request('from_date'))->format('d-m-Y') }}</strong>@endif
                                @if(request('to_date')) to <strong>{{ \Carbon\Carbon::parse(request('to_date'))->format('d-m-Y') }}</strong>@endif
                                &nbsp;|&nbsp; Total RX: <strong>{{ $rxDetails->sum('rx_count') }}</strong>
                            </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped" id="rxTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Zone</th>
                                            <th>Region</th>
                                            <th>HQ</th>
                                            <th>RX Count</th>
                                            <th class="no-export text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rxDetails as $rx)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($rx->date)->format('d-m-Y') }}</td>
                                            <td>{{ $rx->zone->name ?? 'N/A' }}</td>
                                            <td>{{ $rx->region->name ?? 'N/A' }}</td>
                                            <td>{{ $rx->hq->name ?? 'N/A' }}</td>
                                            <td>{{ $rx->rx_count }}</td>
                                            <td class="text-right">
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light mr-2 edit-rx" href="#"
                                                        data-id="{{ $rx->id }}"
                                                        data-count="{{ $rx->rx_count }}"
                                                        data-date="{{ $rx->date }}">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('rx.destroy', $rx->id) }}" method="POST" style="display:inline;" data-confirm="Delete this RX record?">
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
                                    @if($rxDetails->count() > 0)
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="4" class="text-right">Total RX</td>
                                            <td>{{ $rxDetails->sum('rx_count') }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit RX Modal -->
<div class="modal fade" id="editRXModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update RX Log</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRXForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>RX Count</label>
                        <input type="number" name="rx_count" id="edit_rx_count" class="form-control" required min="1">
                    </div>
                    <div class="form-group mt-3">
                        <label>Date</label>
                        <input type="date" name="date" id="edit_date" class="form-control" required readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.footer')

<script>
$(document).ready(function() {
    // DataTable init
    $('#rxTable').DataTable({
        dom: 'lfrtip',
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });

    // Use event delegation so DataTables doesn't break the bindings
    $(document).on('click', '.edit-rx', function(e) {
        e.preventDefault();
        $('#edit_rx_count').val($(this).data('count'));
        $('#edit_date').val($(this).data('date'));
        $('#editRXForm').attr('action', '/rx-details/' + $(this).data('id'));
        $('#editRXModal').modal('show');
    });
});
</script>
