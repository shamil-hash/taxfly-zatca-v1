<style>
  .modal {
    position: fixed;
    top: 30%;
  }

  #parent {
    margin-top: 2%;
    margin-left: 10%;
    margin-right: 10%;
  }
</style>
<form action="createcategory" method="POST" id="myformCategory" enctype="multipart/form-data">
  @csrf
  <div class="modal fade text-left" id="Createcategory" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <input type="text" class="form-control" name="categoryname" placeholder="Enter Category Name">
          <input type="file" class="form-control" name="image">

          <br>
          <button type="submit" class="btn btn-primary" id="submitBtnC">SUBMIT</button>
        </div>
      </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("myformCategory");
        const submitBtn = document.getElementById("submitBtnC");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>