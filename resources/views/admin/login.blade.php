<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Maven+Pro&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>
        body {
            background-color: #187f6a;
            font-family: 'Maven Pro', sans-serif;
        }

        .login-div {
            border-radius: 25px;
            margin-top: 170px;
            margin-left: auto;
            margin-right: auto;
            width: 400px;
            background-color: white;
            padding: 60px;
        }

        .display {
            display: flex;
            margin-left: 5px;
            margin-top: 5px;
        }
    </style>

</head>

<body>
    @if(session('alert'))
    <div class="row display" align="center">
        <div class="col-md-5">
            <div class="alert alert-danger ">
                {{session('alert')}}
            </div>
        </div>
    </div>
    @endif
    <form action="adminuser" method="POST">
        @csrf
        <div class="login-div" align="center">
            <div class="title" style="font-size:40px;">Login <br> Admin</div>
            <br>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="USERNAME">
                <br>
                <input type="password" class="form-control" name="password" placeholder="PASSWORD">
                <br>
                <br>
                <button type="submit" class="btn btn-info">LOGIN</button>
            </div>
        </div>
    </form>
    <div align="center">
        <span>
            Designed by NETPLEX
        </span>
    </div>
</body>

</html>
