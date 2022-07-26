@extends('layouts.header')

@section('content')
<body>
<div class="container">

  <h4>List Users</h4>
  <table class="table" id="datatable">
    <thead>
      <tr>
        <th>Name</th>
        <th>DOB</th>
        <th>Email</th>
        <th>Mobile_no</th>
        <th>Gender</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
     <?php /* @foreach($users as $each)
      <tr>
        <td>{{ $each->name }}</td>
        <td>{{ $each->name }}</td>
        <td>{{ $each->dob }}</td>
        <td>{{ $each->email_id }}</td>
        <td>{{ $each->mobile_no }}</td>
        <td>{{ $each->gender }}</td>
        <td>{{ $each->role_name }}</td>
        <td>
          <a href="{{ URL::to('/create-user/'.$each->id) }}">Edit</a>
          <a href="javascript:void(0)" onclick="deleteUser(<?php echo $each->id; ?>)">Delete</a>
        </td>
      </tr>
      @endforeach */ ?>
    </tbody>
  </table>
</div>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css"/>
<!-- <link href="cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC123" crossorigin="anonymous"/> -->
<script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

  var table;
	$(function () {
		table = $('#datatable').DataTable({
			"dom": '<"top"i>rt<"bottom"lp><"clear">',
			paging: true,
			ordering: true,
			order: [[ 0, "desc" ]],
			info: true,
			"processing": true,
			"serverSide": true,
			"lengthChange": true,
			"searching": false,
      "responsive": true,
			"ajax":{
			"url": "<?php echo URL::to('ajax_list_users'); ?>",
			"dataType": "json",
			"type": "POST",
			"data": function (data) {
					data._token = "<?php echo csrf_token(); ?>";
					data.filters = $('#filterform').serializeArray();
				}
			},
			"columns":  [{ "data": "name" },{ "data": "dob" },{ "data": "email_id" },{ "data": "mobile_no" },{ "data": "gender" },{ "data": "role_name" },{ "data": "options" ,"orderable": false }]
		});
	});

  function  deleteUser(user_id){
    var vdatatype= "json";
    var formData = new FormData();

    var option ='addeditSave';
    var ajaxcsrf=$('meta[name="curd-csrf-token"]').attr('content');
    formData.append( "option", option );
    formData.append( "_token", '{{csrf_token()}}' );
    formData.append( "user_id", user_id );
    Swal.fire({
        icon: 'warning',
        title: 'Do you want to delete user?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

            $.ajax({
                url: "<?php echo URL::to('delete_user'); ?>",
                type: "POST",
                data: formData,
                dataType: vdatatype,
                contentType: false,
                processData: false,
                success: function (data) {
                    if(data.status){
                      window.location.href = data.redirect;
                    }
                }
            });

        }
      })
    
}

  
</script>
</body>
@endsection
