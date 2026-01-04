<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Super User</title>
    @include('layouts/superusersidebar')
    <style>
        input[type=text],
        input[type=password],
        input[type=file] {
            border-radius: 5px;
            width: 100%;
        }

        .custom-textarea {
            width: 100%;
            resize: vertical;
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
            <div class="alert alert-danger">
                {{ Session::get('failed') }}
            </div>
        @endif
        <div class="container d-flex justify-content-center">
            <div>
                <form action="admincreate" method="POST" enctype="multipart/form-data" id="createadmin"
                    name="createadmin" onsubmit="return(validateSearch());">
                    @csrf
                    <h2 class="text-center">Create Admin</h2>
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Full Name <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control name" name="name" placeholder="name"
                                        value="{{ old('name') }}">
                                </div>
                                <span style="color:red">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Username <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control username" name="username"
                                        placeholder="Username" value="{{ old('username') }}">
                                </div>
                                <span style="color:red">
                                    @error('username')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Password <span
                                            style="color: red;">*</span></span>
                                    <input type="password" class="form-control password" name="password"
                                        placeholder="Password">
                                </div>
                                <span style="color:red">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Phone <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control phone" name="phone"
                                        placeholder="Phone Number" value="{{ old('phone') }}">
                                </div>
                                <span style="color:red">
                                    @error('phone')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Location <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control location" name="location"
                                        placeholder="Location" value="{{ old('location') }}">
                                </div>
                                <span style="color:red">
                                    @error('location')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Address <span
                                            style="color: red;">*</span></span>
                                    <textarea name="address" id="address" cols="30" rows="3" class="form-control custom-textarea address"
                                        placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                                <span style="color:red">
                                    @error('address')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Email <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control email" name="email"
                                        placeholder="Email" value="{{ old('email') }}">
                                </div>
                                <span style="color:red">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Currency <span
                                            style="color: red;">*</span></span>
                                    <select name="currency" class="form-control currency">
                                        <option value="">-select currency-</option>
                                        <option value="INR">INR</option>
                                        <option value="AED">AED</option>
                                        <option value="SAR">SAR</option>
                                        <option value="OMR">OMR</option>
                                    </select>
                                </div>
                                <span style="color:red">
                                    @error('currency')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">P O Box</span>
                                    <input type="text" class="form-control po_box" name="po_box"
                                        placeholder="PO BOX" value="{{ old('po_box') }}">
                                </div>
                                <span style="color:red">
                                    @error('po_box')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Postal Code </span>
                                    <input type="text" class="form-control postal_code" name="postal_code"
                                        placeholder="Postal Code" value="{{ old('postal_code') }}">
                                </div>
                                <span style="color:red">
                                    @error('postal_code')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">CR Number </span>
                                    <input type="text" class="form-control cr_number" name="cr_number"
                                        placeholder="CR Number" value="{{ old('cr_number') }}">
                                </div>
                                <span style="color:red">
                                    @error('cr_number')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Company LOGO (718 x 347)
                                        <span style="color: red;">*</span></span>
                                    <input type="file" name="logo" class="form-control logo">
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Transaction ID Default <span
                                            style="color: red;">*</span></span>
                                    <input type="text" class="form-control transpart" name="transpart"
                                        placeholder="Transaction ID Start Default">
                                </div>
                                <span style="color:red">
                                    @error('transpart')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">TAX <span
                                            style="color: red;">*</span></span>
                                    <select name="tax" class="form-control tax">
                                        <option value="">-select tax mode</option>
                                        <option value="VAT">VAT</option>
                                        <option value="GST">GST</option>

                                    </select>
                                </div>
                                <span style="color:red">
                                    @error('tax')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon5">Country </span>
                                    <select name="country" class="form-control tax">
                                        <option value="">-select country</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="United Arab Emirates">United Arab Emirates</option>

                                    </select>
                                </div>

                            </div>
                        </div>
                        <br />
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary submitadmin"
                                id="submitAdminBtn">SUBMIT</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
    $(".phone").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("input.location");
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
    $(".location").keydown(function(event) {
        if (event.keyCode == 13) {
            textboxes = $("textarea.address");
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
    $(".address").keydown(function(event) {
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
            textboxes = $("select.currency");
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
    $(".currency").keydown(function(event) {
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
    $(".tax").keydown(function(event) {
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
            textboxes = $("button.submitadmin");
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
        $('#createadmin').keypress(function(e) { //use form id
            if (e.which == 13) {
                validateSearch(); //-- to validate form
                $('#createadmin').submit(); // use form id
                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("createadmin");
        const submitBtn = document.getElementById("submitAdminBtn");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>
