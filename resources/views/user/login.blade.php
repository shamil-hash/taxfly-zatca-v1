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
    </style>

</head>

<body>
    <form action="user" method="POST">
        @csrf
        @if(Session::get('fail'))
        <div class="alert alert-danger">
            {{Session::get('fail')}}
        </div>
        @endif
        <div class="login-div" align="center">
            <div class="title" style="font-size:40px;">Login <br> User</div>
            <br>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="USERNAME">
                <span class="text-danger">@error('username'){{$message}} @enderror</span>
                <br>
                <input type="password" class="form-control" name="password" placeholder="PASSWORD">
                <span class="text-danger">@error('password'){{$message}} @enderror</span>
                <br>
                <br>
                <button type="submit" class="btn btn-info">LOGIN</button>
            </div>
        </div>
    </form>
    <div align="center">
        <span>
            Powered by NETPLEX
        </span>
    </div>
</body>

</html>
