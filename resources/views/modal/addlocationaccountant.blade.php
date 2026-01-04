<head>
</head>
<style>
  #parent {
    margin-top: 2%;
    margin-left: 10%;
    margin-right: 10%;
  }
</style>
<form action="addlocationaccountant" method="POST">
  @csrf
  <div class="modal fade text-left" id="Locations" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-demission="modal" aria-label="Close"></button>
          <div>
            <div align="center">
              <div class="form-group">
                <div align="left" id="parent">
                  <h2 class="modal-title" ALIGN="CENTER">Add Location</h2>
                  <input type="hidden" class="form-control" name="user_id" id="user_id" placeholder="id">
                  <br>
                  @foreach($branches as $branch)
                  <input type="checkbox" id="{{$branch['id']}}" name="location_id[]" value="{{$branch['id']}}">
                  <label for="check">{{$branch['location']}}({{$branch['company']}})</label>
                  <br>
                  @endforeach
                </div>
                <button type="submit" class="button">SUBMIT</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</form>