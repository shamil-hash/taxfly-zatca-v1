<head>
</head>
<style>
    #parent {
        margin-top: 2%;
        margin-left: 10%;
        margin-right: 10%;
    }
</style>
<form action="addmodules" method="POST" onsubmit="return validateForm()">
    @csrf
    <div class="modal fade text-left" id="Roles" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-demission="modal" aria-label="Close"></button>
                    <div>
                        <div align="center">
                            <div class="form-group col-md-12">
                                <h2 class="modal-title" ALIGN="CENTER">Add Roles</h2>
                                <input type="hidden" class="form-control" name="user_id" id="user_id"
                                    placeholder="id">
                                <div class="col-md-6">
                                    <div align="left" id="parent">
                                        <input type="checkbox" class="roleCheckbox"  id="selectAll" onClick="toggleSelectAll(this)">
                                        <label for="selectAll">Select All</label>
                                        <br>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="bill" name="role[]" value="1">
                                        <label for="check">Billing Shop
                                        </label>
                                        <br>
                                        Billing
                                        <br>
                                        Return
                                        <br>
                                        Return History
                                        <br>
                                        Transaction History
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="inventory" name="role[]" value="3">
                                        <label for="inventory">Inventory</label>
                                        <br>

                                        Add Product
                                        <br>
                                        Stock
                                        <br>
                                        Purchase
                                        <br>
                                        Purchase History
                                        <br>

                                        <input type="checkbox"  class="roleCheckbox" id="accountant" name="role[]" value="11">
                                        <label for="accountant">Accountant</label>
                                        <br>
                                        Company Expenses
                                        <br>
                                        Acounts Report
                                        <br>
                                        Final Report
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="branches" name="role[]" value="8">
                                        <label for="branches">Branches</label>
                                        <br>
                                        Create Branches<br>
                                        List Branch
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="user" name="role[]" value="12">
                                        <label for="user">USER</label>
                                        <br>
                                        Create User
                                        <br>
                                        List User
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="credit" name="role[]" value="14">
                                        <label for="credit">CREDIT</label>
                                        <br>
                                        Create Credit User<br>
                                        Customer Summary <br>
                                        Customer Sales<br>
                                        List Credit User
                                        <br>
                                        <!--<input type="checkbox" id="credit" name="role[]" value="15">-->
                                        <!--<label for="credit">PLEXPAY</label>-->
                                        <!--<br>-->
                                        <input type="checkbox" class="roleCheckbox" id="supplier" name="role[]" value="16">
                                        <label for="supplier">SUPPLIER</label>
                                        <br>
                                        Create Supplier<br>
                                        List Supplier
                                        <br>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div align="left" id="parent">
                                        <br />
                                        <input type="checkbox" class="roleCheckbox" id="salesorder" name="role[]" value="17">
                                        <label for="salesorder">Sales Order</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="deliverynote" name="role[]" value="18">
                                        <label for="deliverynote">Delivery Note</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="purchaseorder" name="role[]" value="19">
                                        <label for="purchaseorder">Purchase Order</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="quotation" name="role[]" value="20">
                                        <label for="quotation">Quotation</label>
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="performance_invoice" name="role[]" value="21">
                                        <label for="performance_invoice">Proforma Invoice</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="salesorder_to_invoice" name="role[]" value="22">
                                        <label for="salesorder_to_invoice">Salesorder to Invoice</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="sunmi_print" name="role[]" value="23">
                                        <label for="sunmi_print">Sunmi Print</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="pdf_prints" name="role[]" value="24">
                                        <label for="pdf_prints">PDF Prints</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="all_pdfsunmi_prints" name="role[]"
                                            value="25">
                                        <label for="all_pdfsunmi_prints">Sunmi PDF Print</label>
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="quotation_to_invoice" name="role[]"
                                            value="26">
                                        <label for="quotation_to_invoice">Quotation to Invoice</label>
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="purchaseorder_to_purchase" name="role[]"
                                            value="27">
                                        <label for="purchaseorder_to_purchase">Purchase Order to Purchase</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="Quotation_to_sales_order" name="role[]"
                                            value="28">
                                        <label for="Quotation_to_sales_order">Quotation to Sales Order</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="bill_without_header" name="role[]"
                                            value="29">
                                        <label for="bill_without_header">Receipt Without Header</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="new_layout" name="role[]"
                                           value="30">
                                        <label for="new_layout">Change Layout</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="to_delivery" name="role[]"
                                            value="31">
                                        <label for="to_delivery">To Delivery</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="employee" name="role[]"
                                            value="32">
                                        <label for="employee">Employee</label>
                                        <br>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <button type="submit" class="btn btn-primary">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    function toggleSelectAll(source) {
        let checkboxes = document.querySelectorAll('.roleCheckbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
    function validateForm() {
        let checkboxes = document.querySelectorAll('.roleCheckbox');
        let isChecked = false;

        // Check if at least one checkbox is selected
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isChecked = true;
            }
        });

        // If no checkbox is selected, show an alert and prevent form submission
        if (!isChecked) {
            alert("Please select at least one role before submitting.");
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }
</script>
