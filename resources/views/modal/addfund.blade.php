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
<form action="addfundcredit" method="POST">
    @csrf
    <div class="modal fade text-left" id="Addfund" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="form group row">
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="hidden" class="form-control" id="creditid" name="creditid" style="width:80%;">
                                <label>
                                    USER ID:
                                </label>
                                <input type="text" class="form-control" id="fundusername" name="fundusername" placeholder="USER ID:" style="width:80%;">
                            </div>
                            <div class="col-sm-4">
                                <label>
                                    DUE:
                                </label>
                                <input type="text" class="form-control" id="due" name="due" style="width:80%;" readonly>
                            </div>
                            <div class="col-sm-4">
                                <label for="categoryname">
                                    ADD FUND:
                                </label>
                                <!-- <div align="right"> -->
                                <input type="text" class="form-control" id="" name="addedfund" placeholder="FUND" style="width:80%;">
                                <!-- </div> -->
                                <BR>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>