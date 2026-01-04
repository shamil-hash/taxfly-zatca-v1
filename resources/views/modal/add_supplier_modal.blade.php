<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    @csrf
                    <div class="form-group">
                        <label for="customer_name">Name</label>
                        <input type="text" class="form-control" id="customer_name" name="supplier_name" required>
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
            url: "{{ route('add.supplier') }}", // Laravel route
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Append new supplier to the dropdown
                    $('#supplierdata').append(`<option value="${response.supplier.name}" data-id="${response.supplier.id}" selected>${response.supplier.name}</option>`);

                    // Set hidden supplier ID
                    $('#supp_id').val(response.supplier.id);

                    // Close the modal
                    $('#addCustomerModal').modal('hide'); // Fixed ID

                    // Reset the form
                    $('#addCustomerForm')[0].reset(); // Fixed form ID
                }
            },
            error: function(error) {
                console.log(error);
                alert("Error adding supplier!");
            }
        });
    });
});

    
    
    
    </script>
   
