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
<form action="createunit" method="POST" id="myformunit">
  @csrf
  <div class="modal fade text-left" id="Createunit" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <input type="text" class="form-control" name="unit" placeholder="Enter Measuring Unit">
          <br>
          <button type="submit" class="btn btn-primary" id="submitBtnU">SUBMIT</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("myformunit");
        const submitBtn = document.getElementById("submitBtnU");

        form.addEventListener("submit", function(e) {
            // Prevent the form from submitting multiple times
            submitBtn.disabled = true;
            submitBtn.innerText = "Submitting...";

            // Allow the form to submit normally
            return true;
        });
    });
</script>