<style>
    .modal {
        position: fixed;
        top: 30%;
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #ffffff;
    }

    .modal-content {
        padding-left: 5%;
        padding-right: 5%;
        padding-top: 5%;
        padding-bottom: 5%;
    }

    .modal-lg {
        width: 40%;
    }

    #parent {
        margin-top: 2%;
        margin-left: 10%;
        margin-right: 10%;
    }
</style>
<form action="/rawilk_print_receipt" method="POST">
    @csrf
    <div class="modal fade text-left" id="PrinterM" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="row">
                        <div class="form-group">
                            <label for="">Printers:</label>
                            <select name="printerdrop" id="printerdrop" class="form-control">
                                <option value="">select printer</option>
                            </select>

                            <input type="hidden" name="trans_id" id="trans_id">
                            <!--<input type="text" name="printer_status" id="printer_status"> -->
                            <br />
                            <button type="submit" class="btn btn-primary">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>