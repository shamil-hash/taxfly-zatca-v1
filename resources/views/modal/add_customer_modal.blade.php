<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    @csrf
                    <div class="form-group">
                        <label for="customer_name">Name</label>
                        <input type="text" class="form-control" id="customer_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="credit_limit">Credit Limit</label>
                        <input type="number" class="form-control" id="credit_limit" name="credit_limit">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#addCustomerForm").submit(function(e) {
            e.preventDefault(); // Prevent default form submission
    
            $.ajax({
                type: "POST",
                url: "{{ route('add.customer') }}", // Your Laravel route
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // ✅ Append new customer to dropdown (without selecting)
                        $("#user_id").append(
                            `<option value="${response.customer.id}" 
                                data-id="${response.customer.id}" 
                                data-current_lamount="${response.customer.l_amount}" 
                                data-balance="0" 
                                data-name="${response.customer.name}">
                                ${response.customer.name}
                            </option>`
                        );
    
                        // ✅ Close modal without refresh
                        $("#addCustomerModal").modal("hide");
                        $("#addCustomerForm")[0].reset(); // Clear form fields
                    } else {
                        alert("Something went wrong!");
                    }
                },
                error: function(error) {
                    console.log(error);
                    alert("Error adding customer!");
                }
            });
        });
    });
    
    
    
    </script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    let barcodeInput = document.getElementById("barcodeScanner"); // Ensure this ID matches your barcode input field
    let addCustomerModal = document.getElementById("addCustomerModal");

    // ✅ When modal opens, ensure barcode input is NOT focused
    $('#addCustomerModal').on('shown.bs.modal', function () {
        if (barcodeInput) {
            barcodeInput.blur(); // Remove focus from barcode
        }
    });

    // ✅ Prevent barcode input from refocusing when typing inside modal fields
    document.querySelectorAll("#addCustomerModal input").forEach(input => {
        input.addEventListener("focus", function () {
            if (barcodeInput) {
                barcodeInput.blur();
            }
        });
    });

    // ✅ Prevent barcode scanner focus when clicking inside modal
    addCustomerModal.addEventListener("click", function () {
        if (barcodeInput) {
            barcodeInput.blur();
        }
    });

    // ✅ Extra: Hide keyboard focus if needed (for mobile scanners)
    document.querySelectorAll("#addCustomerModal input").forEach(input => {
        input.addEventListener("click", function (e) {
            e.stopPropagation(); // Stop barcode input from regaining focus
        });
    });
});


</script>
