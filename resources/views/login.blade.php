<!DOCTYPE html>
<html lang="en">
<head>
  <title>User Management</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h4><b>Login Form</b></h4>
  <form id="login_form" autocomplete="off">
    <div class="form-group row">
      <div class="col-xs-4">
        <label for="email">Email ID:</label>
        <input type="email" class="form-control" autocomplete="off" id="email" placeholder="Enter email" name="email">
        <span class="error"></span>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-xs-4">
        <label for="pwd">Password:</label>
        <input type="password" class="form-control" autocomplete="off" id="password" placeholder="Enter password" name="password">
        <span class="error" id="other_error"></span>
      </div>
      
    </div>
  </form>
  <button type="submit" class="btn btn-default" id="login_btn" onclick="user_login()">Login</button>
</div>

</body>

<script>



function  user_login(){
  var vdatatype= "json";
  var formData = new FormData($('#login_form')[0]);

  var option ='addeditSave';
  var ajaxcsrf=$('meta[name="curd-csrf-token"]').attr('content');
  formData.append( "option", option );
  formData.append( "_token", '{{csrf_token()}}' );
  $('.error').html('');
  $("#other_error").text('');

  $.ajax({
      url: "<?php echo URL::to('post_login'); ?>",
      type: "POST",
      data: formData,
      dataType: vdatatype,
      contentType: false,
      processData: false,
      success: function (data) {
          if(data.status){
            window.location.href = data.redirect;
          }else{
              $("#other_error").text(data.message);
          }
      },
      error: function(xhr, status, error) {
          $.each(xhr.responseJSON.errors, function (key, value) {
              $('#login_form [name="' + key + '"]').nextAll("span.error").html(value);
          });    
      }
  });
}
</script>
</html>
