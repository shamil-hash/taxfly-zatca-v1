<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SuperUser</title>
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
    <!-- Page Content Holder -->
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
        <h2>List Billing Desks</h2>
        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Name</th>
                    <th width="10%">Username</th>
                    <th width="10%">Created At</th>
                    <th width="5%">Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->admin_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('restore-data') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="zip_file">ZIP File:</label>
            <input type="file" id="zip_file" name="zip_file" required>
            <button type="submit">Restore Data</button>
        </form>
    </div>
</body>

</html>
