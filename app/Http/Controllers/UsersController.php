<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use DB, URL, Auth;


class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * users edit functionality
     */
    public function createUser($id = ""){
        $user_data = [];
        if(!empty($id)){
            $user_data = User::where('id',$id)->first();
        }
        return view('add_users',compact('user_data'));
    }

    public function saveUser(Request $request){
        $input = $request->all();
        $response['status'] = true;
        $response['message'] = "";

        $validate_arr['name'] = 'required';
        $validate_messages['name.required'] = 'Please enter name';
        
        $validate_arr['role_id'] = 'required';
        $validate_messages['role_id.required'] = 'Please select role';

        $validate_arr['mobile_no'] = ['required','min:6000000000','numeric','digits:10'];
        $validate_messages['mobile_no.required'] = 'Please enter mobile number';
        $validate_messages['mobile_no.digits'] = 'Please enter valid mobile number';
        $validate_messages['mobile_no.numeric'] = 'Please enter valid mobile number';
        $validate_messages['mobile_no.min'] = 'Please enter valid mobile number';

        $validate_arr['gender'] = 'required';
        $validate_messages['gender.required'] = 'Please select gender';
        $validate_arr['email_id'] = ['required','email'];
        if(isset($input['user_id']) && !empty($input['user_id'])){
            $validate_arr['email_id'][] = 'unique:users,email_id,'.$input['user_id'];
        }else{
            $validate_arr['email_id'][] = 'unique:users,email_id';
        }
        $validate_messages['email_id.required'] = 'Please enter email id';
        $validate_messages['email_id.email'] = 'Please enter valid email id';
        
        $validate_arr['dob'] = 'required';
        $validate_messages['dob.required'] = 'Please select date of birth';

        $validate_arr['profile_image'] = ['image','mimes:jpeg,png,jpg,gif','max:2048'];
        $validate_messages['profile_image.required'] = 'Please select date of birth';

        if(!isset($input['user_id']) || empty($input['user_id'])){
            $validate_arr['password'] = 'required';
            $validate_messages['password.required'] = 'Please enter password';
            $validate_arr['profile_image'][] = 'required';
        }
        $validatedData = $request->validate($validate_arr, $validate_messages);

        $insert_array = [];
        if($request->file('profile_image')){
            if (!file_exists(storage_path('app/public/profile_images'))){
                mkdir(storage_path('app/public/profile_images'), 0777, true);
                //die('Failed to create directories...');
            }
            $file= $request->file('profile_image');
            $filename =  time().'.'.$file->extension();
            $file-> move(storage_path('app/public/profile_images'), $filename);
            $insert_array['profile_image'] = $filename;
        }

        $insert_array['name'] = isset($input['name'])? $input['name']: NULL;
        $insert_array['email_id'] = isset($input['email_id'])? $input['email_id']: NULL;
        if(isset($input['password']) && !empty($input['password'])){
            $insert_array['password'] = bcrypt($input['password']);
        }
        $insert_array['role_id'] = isset($input['role_id'])? $input['role_id']: NULL;
        $insert_array['mobile_no'] = isset($input['mobile_no'])? $input['mobile_no']: NULL;
        $insert_array['gender'] = isset($input['gender'])? $input['gender']: NULL;
        $insert_array['dob'] = isset($input['dob'])? $input['dob']: NULL;
        $insert_array['created_at'] = date("Y-m-d H:i:s");
        $insert_array['updated_at'] = date("Y-m-d H:i:s");
        
        if(isset($input['user_id']) && !empty($input['user_id'])){
            User::where('id',$input['user_id'])->update($insert_array);
            $response["message"] = "user updated successfully";
        }else{
            User::insert($insert_array);
            $response["message"] = "user added successfully";
        }
        return $response;
    }
    

    public function deleteUser(Request $request){
        $input = $request->all();
        if(isset($input['user_id']) && !empty($input['user_id'])){
            $user_details = User::where('id',$input['user_id'])->first();
            if(isset($user_details->profile_image) && !empty($user_details->profile_image)){
                unlink(storage_path('app/public/profile_images/'.$user_details->profile_image));
            }
            User::where('id',$input['user_id'])->delete();
        }
        
        return [ "status" => true, "message" => "user deleted successfully", 'redirect' => URL::to('list-users')];
    }

    public function listUsers(Request $request){
        return view('users_list');
    }

    public function ajaxListUsers(Request $request){
        $input = $request->all();
        $columns= $request->input('columns');
        $limit = $request->input('length');
        $start = $request->input('start');
        $users_query = DB::table('users as u')
            ->join('roles as r', 'u.role_id','r.role_id')
            ->where('u.id','!=',Auth::user()->id)
            ->orderBy('u.created_at');
        $total_count = $users_query->count();
		$rows = $users_query->offset($start)->limit($limit)->get();
        $data = [];
        foreach ($rows as $row)
        {
            foreach($columns as $column){
                $c=$column['data'];
                if(isset($row->$c))
                $nestedData[$c] = $row->$c;
                else
                $nestedData[$c] = "";
            }
            
            $nestedData['options'] = "";
            if(Auth::user()->role_id === 1){
                //Super Admin have all permissions
                $link_url = URL::to('create-user/'.$row->id);
                $nestedData['options'] = "<a href='".$link_url."')>Edit</a>";
                $nestedData['options'] .= "&nbsp&nbsp<a href='javascript:void(0)' onclick='deleteUser(".$row->id.")'>Delete</a>";
            }

            if(in_array(Auth::user()->role_id,[2]) && !in_array($row->role_id,[1,2])){
                //Admin user not have permission to edit users
                //Admin user have permission to delete normal users
                //Admin user not have permission to delete admin users and super admin users
                $nestedData['options'] = "&nbsp&nbsp<a href='javascript:void(0)' onclick='deleteUser(".$row->id.")'>Delete</a>";
            }

            $data[] = $nestedData; 
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($total_count),
            "recordsFiltered" => intval($total_count),
            "data"            => $data   
            );
        echo json_encode($json_data); 
    }

}
