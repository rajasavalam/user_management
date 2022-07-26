@extends('layouts.header')

@section('content')
<body>
<div class="container">
  <h4>Create User Form</h4>
  <form id="add_user_form" enctype="multipart/form-data" autocomplete="off"> 
    <div class="form-group row">
      <div class="col-xs-4">
        <label for="">Name:</label>
        <input type="hidden" name="user_id" value="{{ isset($user_data->id)? $user_data->id: '' }}">
        <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" value="{{ isset($user_data->name)? $user_data->name: '' }}">
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="email">Role:</label>
        <select class="form-control" id="role_id" name="role_id">
          <option value="">Select</option>
          <option value="1">Super Admin</option>
          <option value="2">Admin</option>
          <option value="3">User</option>
        </select>
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="">Mobile Number:</label>
        <input type="text" class="form-control" maxlength="10" id="mobile_no" placeholder="Enter Mobile Number" name="mobile_no" value="{{ isset($user_data->mobile_no)? $user_data->mobile_no: '' }}">
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="email">Email:</label>
        <input type="text" class="form-control" id="email_id" placeholder="Enter email" name="email_id" value="{{ isset($user_data->email_id)? $user_data->email_id: '' }}">
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="">Gender:</label>
        <select class="form-control" id="gender" name="gender">
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="">DOB:</label>
        <input type="date" class="form-control" id="dob" name="dob" value="{{ isset($user_data->dob)? date('d-m-Y',strtotime($user_data->dob)): '' }}">
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="">Upload Profile Image:</label>
        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="loadImage(event)">
        <span class="error"></span>
      </div>

      <?php 
        $image_path = (isset($user_data->profile_image) && !empty($user_data->profile_image))? env('APP_URL').'/storage/app/public/profile_images/'.$user_data->profile_image: "";
      ?>
      <div class="col-xs-4" class="show_image">
          <img src="{{ $image_path }}" alt="profile Pic" id="profile_preview" name="profile_preview" height="100" width="100" style="<?php echo (empty($image_path))? 'display:none': ''; ?>">
      </div><br>
    </div>

    <div class="form-group row">
      <div class="col-xs-4">
        <label for="pwd">Password:</label>
        <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
        <span class="error"></span>
      </div>
    </div>

    <div class="form-group">
      <button type="button" class="btn btn-default" id="submit_btn" onclick="save_user()">Submit</button>
    </div>
  </form>
  
</div>

</body>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

function loadImage(event) {
    $('#profile_preview').attr('src','');
    $('#profile_preview').attr('src', URL.createObjectURL(event.target.files[0]));
    $('#profile_preview').show();
};

$(document).ready(function(){
    <?php if(isset($user_data->role_id) && !empty($user_data->role_id)){?>
    $("#role_id").val("<?php echo $user_data->role_id; ?>");
    $("#gender").val("<?php echo $user_data->gender; ?>");
    $("#dob").val("<?php echo $user_data->dob; ?>");
    <?php } ?>
});

function  save_user(){

  var formData = new FormData();
  var vdatatype= "json";
  
  formData = new FormData($('#add_user_form')[0]);

  var option ='addeditSave';
  var ajaxcsrf=$('meta[name="curd-csrf-token"]').attr('content');
  formData.append( "option", option );
  formData.append( "_token", '{{csrf_token()}}' );
  $('.error').html('');

  $.ajax({
      url: "<?php echo URL::to('/save_user'); ?>",
      type: "POST",
      data: formData,
      dataType: vdatatype,
      contentType: false,
      processData: false,
      success: function (data) {
          if(data.status){
            Swal.fire({
              icon: 'success',
              title: data.message,
              showConfirmButton: true
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "{{ URL::to ('list-users') }}";
                //Swal.fire('Saved!', '', 'success')
              }
            });
          }
      },
      error: function(xhr, status, error) {
          
          $.each(xhr.responseJSON.errors, function (key, value) {
              //$('#'+key+'_error').html(value).parent().addClass('has-error');
              $('#add_user_form [name="' + key + '"]').nextAll("span.error").html(value);
          });    
      }
  });
  return false;
}
</script>
@endsection
