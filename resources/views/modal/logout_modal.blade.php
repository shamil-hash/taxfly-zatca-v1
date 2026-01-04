<style>
    .modal {
        text-align: center;
        padding: 0 !important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }

    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }
</style>

<div class="modal fade" id="logout-modal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                @if ($adminUser)
                    <a href="/adminlogout" class="btn btn-danger">Yes, Logout</a>
                @elseif ($softwareUser)
                    <a href="/userlogout" class="btn btn-danger">Yes, Logout</a>
                @elseif ($superUser)
                    <a href="/superuserlogout" class="btn btn-danger">Yes, Logout</a>
                @elseif ($creditUser)
                    <a href="/creditlogout" class="btn btn-danger">Yes, Logout</a>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('logout-link').addEventListener('click', function(event) {
        event.preventDefault();
        $('#logout-modal').modal('show');
    });
</script>
