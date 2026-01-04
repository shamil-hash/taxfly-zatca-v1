<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Maven+Pro&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: #0f5f53;
        font-family: 'Maven Pro', sans-serif;
    }

    .login-div {
        border-radius: 10px;
        margin-top: 170px;
        margin-left: auto;
        margin-right: auto;
        width: 500px;
        background-color: white;
        padding: 60px;
    }

    .mb-25 {
        margin-bottom: 25px;
    }

    .text-white,
    a {
        color: #ffff;
    }

    #ntp:hover {
        color: #dddd;
    }

    .display {
        display: flex;
        margin-left: 5px;
        margin-top: 5px;
    }

    .input-group-addon {
        background-color: #0f5f53;
        border-color: #0f5f53;
        color: white;
    }

    .input-group {
        margin-bottom: 15px;
    }
</style>

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
    <form action="superuseruser" method="POST">
        @csrf
        <div class="login-div" align="center">
            <img class="mb-25" src="{{asset('/images/logoimage/taxfly green png-01 (1).png')}}" style="width:200px;">
            <div class="form-group">
                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input type="text" class="form-control" name="username" placeholder="USERNAME">
                </div>
                <span class="text-danger">@error('username'){{$message}} @enderror</span>

                <div class="input-group input-group-lg">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-lock"></span>
                    </span>
                    <input type="password" class="form-control" name="password" placeholder="PASSWORD">
                </div>
                <span class="text-danger">@error('password'){{$message}} @enderror</span>
                <br>
                <br>
                <button type="submit" style="background-color: #0f5f53" class="btn btn-primary btn-lg">LOGIN NOW</button>
            </div>
        </div>
    </form>
    <div align="center">
         <span style="color: white;" class="glyphicon glyphicon-cog"></span>
        <span class="text-white">
            Powered by <a href="https://netplexsolution.com/" target="_blank" id="ntp">NETPLEX</a>
        </span>
    </div>
</body>

</html>
