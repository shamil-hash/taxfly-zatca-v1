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
    <style>
        select {
            border-radius: 5px;
            width: 30%;
        }

        form {
            margin-right: 5%;
            margin-left: 5%;
            background-color: #ebe9e9;
            padding: 30px;
            border-color: #ddd;
            border-width: 1px;
            border-radius: 4px 4px 0 0;
            -webkit-box-shadow: none;
            box-shadow: none;
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
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('failed'))
            <div class="alert alert-success">
                {{ Session::get('failed') }}
            </div>
        @endif
        <form action="/admineditform" method="POST" enctype="multipart/form-data" id="editadmin" name="editadmin"
            onsubmit="return(validateSearch());">
            @csrf
            <h2>Edit Admin</h2>
            <div>
                <div align="center">
                    <div class="form-group">
                        <br>
                        <input type="hidden" class="form-control" name="id" value="{{ $uid }}">
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Name</span>
                            <input type="text" class="form-control name" name="name" placeholder="name"
                                value="{{ $name }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">User Name</span>
                            <input type="text" class="form-control username" name="username" placeholder="Username"
                                value="{{ $username }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('username')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Email</span>
                            <input type="text" class="form-control email" name="email" placeholder="Email"
                                value="{{ $email }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Location</span>
                            <input type="text" class="form-control location" name="location" placeholder="location"
                                value="{{ $location }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('location')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>

                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Address</span>
                            <textarea name="address" id="address" cols="30" rows="3" class="form-control custom-textarea address"
                                placeholder="Address">{{ $address }}</textarea>
                        </div>
                        <span style="color:red">
                            @error('address')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">PO Box</span>
                            <input type="text" class="form-control po_box" name="po_box" placeholder="PO Box"
                                value="{{ $po_box }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('po_box')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">TR Number</span>
                            <input type="text" class="form-control cr_number" name="cr_number"
                                placeholder="TR Number" value="{{ $cr_number }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('cr_number')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Phone</span>
                            <input type="text" class="form-control phone" name="phone" placeholder="Phone"
                                value="{{ $phone }}" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('phone')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Postal Code</span>
                            <input type="text" class="form-control postal_code" name="postal_code"
                                placeholder="Postal Code" value="{{ $postal_code }}"
                                aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('postal_code')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2"> New Password</span>
                            <input type="password" class="form-control password" name="password"
                                placeholder="New Password" aria-describedby="basic-addon2">
                        </div>
                        <span style="color:red">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Confirm Password</span>
                            <input type="password" class="form-control confirmpassword" name="confirmpassword"
                                placeholder="Confirm Password" aria-describedby="basic-addon2">
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Company LOGO (400 x 109)</span>
                            <input type="file" name="logo" class="form-control logo">
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Transaction Code</span>
                            <input type="text" name="transpart" class="form-control transpart"
                                value="{{ $transpart }}" required>
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Country</span>
                            <input type="text" name="country" class="form-control country"
                                value="{{ $country }}" required>
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">Tax</span>
                            <input type="text" name="tax" class="form-control tax"
                                value="{{ $tax }}" required>
                        </div>
                        <span style="color:red">
                            @error('tax')
                                {{ $message }}
                            @enderror
                        </span>
                        <div ALIGN="RIGHT">
                            <button type="submit" class="btn btn-primary submitedtadmin"
                                id="submitedtadmin">Submit</button>
                        </div>
                    </div>
                </div>
                <div>
        </form>
    </div>
</body>

</html>

<script type="text/javascript">
    var currentBoxNumber = 0;

    $(".name").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.username");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".username").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.email");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".email").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.po_box");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".po_box").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.cr_number");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".cr_number").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.phone");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".phone").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.postal_code");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".postal_code").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.password");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".password").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.confirmpassword");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".confirmpassword").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.logo");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".logo").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.submitedtadmin");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".logo").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.transpart");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
    $(".transpart").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("button.tax");
            currentBoxNumber = textboxes.index(this);
            if (textboxes[currentBoxNumber + 1] != null) {
                nextBox = textboxes[currentBoxNumber + 1];
                nextBox.focus();
                nextBox.select();
            }
            event.preventDefault();
            return false;
        }
    });
</script>

<script>
    $(function() {
        $('#editadmin').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#editadmin').submit(); // use form id
                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("editadmin");
        const submitBtn = document.getElementById("submitedtadmin");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
