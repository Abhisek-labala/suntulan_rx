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
                            <form id="pmdashboard">
                                @csrf
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
                                        <select class="form-control" name="zone" id="zone">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">RC</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="rm" id="rm">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Counsellor</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="educator" id="educator">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2">Doctor Name</label>
                                    <div class="col-md-10">
                                        <select class="form-control" name="doctor" id="doctor">
                                            <option value=""> -- Select -- </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-form-label col-md-2"> </label>
                                    <div class="col-md-10">
                                      <button type="button" name="submit" id="submitbtn" class="btn btn-primary"
                                            >Submit</button>
                                        <button type="button" class="btn btn-secondary"
                                            onclick="downloadpmPatientExcel();">Patient Report</button>
                                        <button type="button" class="btn btn-success"
                                            onclick="downloadDailyReport();">Daily Report</button>
                                    </div>
                                </div>
                            </form>

                 <div class="card mt-3">
                <div class="card-body">
                    <table id="patientTable" class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Gender</th>
                                <th>BMI</th>
                                <th>Consent Form</th>
                                <th>Prescription</th>
                                <th>Counsellor Name</th>
                                <th>RC Name</th>
                                <th>Joint Call With</th>
                                <th>Role</th>
                                <th>Designation</th>
                                <th>City</th>

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
@include('admin.footer');