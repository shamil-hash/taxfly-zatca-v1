<head>
</head>
<style>
    #parent {
        margin-top: 2%;
        margin-left: 10%;
        margin-right: 10%;
    }
</style>
<form action="addroles" method="POST">
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
                                <div class="col-md-7">
                                    <div align="left" id="parent">
                                        <input type="checkbox" id="selectAll" onClick="toggleSelectAll(this)">

                                        <label for="selectAll">Select All</label>
                                        <br><br>

                                        <input type="hidden" class="form-control" name="user_id" id="user_id"
                                            placeholder="id">
                                        <br>
                                        <!-- billing -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '1') { ?>
                                            <input type="checkbox" class="roleCheckbox" id="bill" name="role[]" value="1">
                                            <label for="check">Billing Desk</label>
                                            <br>
                                            Billing
                                            <br>
                                            Return
                                            <br>
                                            Return History
                                            <br>
                                            Transaction History
                                            <br>
                                            <?php } ?>
                                        @endforeach
                                        <!-- inventory -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '3') { ?>
                                            <input type="checkbox" class="roleCheckbox" id="inventory" name="role[]" value="2">
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
                                            <?php } ?>
                                        @endforeach

                                        <!-- ACCOUNTANT -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '11') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="accountant" name="role[]" value="9">
                                            <label for="accountant">Accountant</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach
                                        <!-- BILLING TOUCH -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '13') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="billingtouch" name="role[]" value="10">
                                            <label for="billingtouch">Billing Cafteria</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach
                                        <!-- CREDIT -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '11') { ?>
                                            <br>
                                            <input type="checkbox" id="credit" class="roleCheckbox" name="role[]" value="11">
                                            <label for="credit">CREDIT</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach

                                        <!-- PLEXPAY -->
                                        <!--@ foreach ($users as $user)-->
                                        <!--< ?php if ($user->module_id == '15') { ?>-->
                                        <!--        <br>-->
                                        <!--        <input type="checkbox" id="credit" name="role[]" value="15">-->
                                        <!--        <label for="credit">PLEXPAY</label>-->
                                        <!--        <br>-->
                                        <!--< ?php } ?>-->
                                        <!-- @ endforeach-->

                                        <!-- CREDIT -->
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '17') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="salesorder" name="role[]" value="17">
                                            <label for="salesorder">Sales Order</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach

                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '18') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="deliverynote" name="role[]" value="18">
                                            <label for="deliverynote">Delivery Note</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach

                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '19') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="purchaseorder" name="role[]" value="19">
                                            <label for="purchaseorder">Purchase Order</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach

                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '20') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="quotation" name="role[]" value="20">
                                            <label for="quotation">Quotation</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach
                                        @foreach ($users as $user)
                                            <?php if ($user->module_id == '21') { ?>
                                            <br>
                                            <input type="checkbox" class="roleCheckbox" id="performance_invoice" name="role[]"
                                                value="21">
                                            <label for="performance_invoice">Proforma Invoice</label>
                                            <br>
                                            <?php } ?>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div align="left" id="parent">

                                        <!-- CREDIT USER -->

                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="credit_user" name="role[]" value="26">
                                        <label for="credit_user">Create Customer</label>
                                        <br>
                                          <input type="checkbox" class="roleCheckbox" id="bank" name="role[]" value="24">
                                        <label for="bank">Bank</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="employee" name="role[]" value="25">
                                        <label for="employee">Employee</label>
                                        <br>
                                        <input type="checkbox" class="roleCheckbox" id="supplier" name="role[]" value="22">
                                        <label for="supplier">Supplier</label>
                                        <br>

                                        <input type="checkbox" class="roleCheckbox" id="reports" name="role[]" value="27">
                                      <label for="reports">Reports</label>
                                      <br>
                                      <input type="checkbox" class="roleCheckbox" id="chartofaccounts" name="role[]" value="30">
                                      <label for="chartofaccounts">chart of accounts</label>
                                      <br>
                                      <input type="checkbox" class="roleCheckbox" id="service" name="role[]" value="31">
                                      <label for="service">service</label>
                                      <br>
                                      <input type="checkbox" class="roleCheckbox" id="posbilling" name="role[]" value="32">
                                      <label for="posbilling">pos billing</label>
                                      <br>
                                      <!--<input type="checkbox" class="roleCheckbox" id="creditnote" name="role[]" value="28">-->
                                      <!--<label for="creditnote">Credit Note</label>-->
                                      <!--<br>-->
                                      <!--<input type="checkbox" class="roleCheckbox" id="debitnote" name="role[]" value="29">-->
                                      <!--<label for="debitnote">Debit Note</label>-->
                                      <!--<br>-->


                                        <!-- {{-- @foreach ($users as $user)
                                            < ?php if ($user->module_id == '23') { ?>
                                            <br>
                                            <input type="checkbox" id="sunmi_print" name="role[]" value="23">
                                            <label for="sunmi_print">Sunmi Print</label>
                                            <br>
                                            < ?php } ?>
                                        @endforeach
                                        @foreach ($users as $user)
                                            < ?php if ($user->module_id == '24') { ?>
                                            <br>
                                            <input type="checkbox" id="pdf_prints" name="role[]" value="24">
                                            <label for="pdf_prints">PDF Prints</label>
                                            <br>
                                            < ?php } ?>
                                        @endforeach --}} -->
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <button type="submit" class="button btn btn-primary">SUBMIT</button>
                                <br />
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

</script>
