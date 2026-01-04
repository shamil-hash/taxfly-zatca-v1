<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities</title>
    @include('layouts/superusersidebar')

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #187f6a;
            color: white;
        }

        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 1px solid #010101;
        }
    </style>
</head>

<body>
    <div id="content">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" id="sidebarCollapse" class="btn navbar-btn">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        <span></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/superuserlogout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <h2>List Activities</h2>

        <br />
        <form action="/filter_activities" method="get">
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-sm-4">

                    <div class="col-sm-6">
                        <select class="form-control" name="location" id="location">
                            <option value="">All</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ $location->id == $selectedLocation ? 'selected' : '' }}>
                                    {{ $location->location }}
                                </option>
                            @endforeach
                        </select>

                        <span style=" color:red">
                            @error('location')
                                {{ $message }}
                            @enderror
                        </span>

                        <input type="hidden" value="" name="branch" id="branch">
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    <div class="col-sm-2 arrange">
                        <a href="/exportactivities/" class="btn btn-primary export-link">Export</a>
                    </div>
                </div>
            </div>
        </form>
        <br />

        <table class="table" id="example" width="100%">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="15%">Time & Date</th>
                    <th width="10%">Type</th>
                    <th width="15%">Username</th>
                    <th>Branch</th>
                    <th width="14%">IP Address</th>
                    <th width="25%">Activity</th>
                    <th width="12%">Location</th>
                    <!-- <th width="10%">Region</th>
                    <th width="10%">City</th> -->
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach ($activities as $activitiy)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $activitiy->created_at }}</td>
                        <td>
                            @if ($activitiy->is_admin == '1')
                                Admin
                            @elseif($activitiy->is_user == '1')
                                User
                            @elseif($activitiy->is_credituser == '1')
                                Credit User
                            @endif
                        </td>
                        <td>
                            @if ($activitiy->is_admin == '1')
                                {{ $activitiy->admin_name }}
                            @elseif($activitiy->is_user == '1')
                                {{ $activitiy->user_name }}
                            @elseif($activitiy->is_credituser == '1')
                                {{ $activitiy->credituser_name }}
                            @endif
                        </td>
                        <td>{{ $activitiy->branchname }}</td>
                        <td>{{ $activitiy->ipaddress }}</td>
                        <td>{{ $activitiy->message }}</td>

                        <td>{{ $activitiy->cityName }}, {{ $activitiy->regionName }}, {{ $activitiy->countryName }}
                        </td>
                        <!-- <td>{{ $activitiy->regionName }}</td>
                    <td>{{ $activitiy->cityName }}</td> -->

                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach

            </tbody>
        </table>
    </div>
</body>

</html>

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [
                [0, 'asc']
            ]
        });
    });
</script>

<script>
    $(document).ready(function() {

        var branch = $('#location').val();
        $('#branch').val(branch);

        updateExportLink(branch);

        $('#location').change(function() {

            var branch = $('#location').val();
            $('#branch').val(branch);

            updateExportLink(branch);
        });

        function updateExportLink(branch) {

            var exportUrl

            if (branch === '' || branch === null) {
                exportUrl = '/exportactivities';
            } else {
                exportUrl = '/exportactivities/' + branch;
            }

            $('a.export-link').attr('href', exportUrl);
        }
    });
</script>
