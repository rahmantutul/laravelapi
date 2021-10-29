<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
       public function addUser(Request $request){
        if($request->isMethod('post')){
            $userData=$request->input();
            // if(empty($userData['name']) || empty($userData['email']) || empty($userData['password'])){
            //     $error_massage= "Some data is missing!";
            //     // return response()->json(['status'=>false, 'massage'=>$massage],422);
            // }
            // if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            //    $error_massage= "Enter valid email!";
            //     // return response()->json(['status'=>false, 'massage'=>$massage],422);
            // }
            // $userEmail = User::where('email',$userData['email'])->count();
            // if($userEmail>0){
            //     $error_massage= "Email is already exist!";
            //     // return response()->json(['status'=>false, 'massage'=>$massage],422);
            // }
            // if(isset($error_massage)){
            //     return response()->json(['status'=>false, 'massage'=>$error_massage],422);
            // }
            $rules=[
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:6'
            ];
            $validator= Validator::make($userData, $rules);
            if($validator->fails()){
                 return response()->json($validator->errors(),422);
            }
            
            $data= new User;
            $data->name=$userData['name'];
            $data->email=$userData['email'];
            $data->password=bcrypt($userData['password']);
            $data->save();
            return response()->json(['massage'=>'Data saved successfully!']);
        }
    }
    public function addUsers(Request $request){
        if($request->isMethod('post')){
            $userData=$request->input();
           foreach($userData['users'] as $key=>$value){
                $data= new User;
                $data->name=$value['name'];
                $data->email=$value['email'];
                $data->password=bcrypt($value['password']);
                $data->save();
           }
           return response()->json(['massage'=>'Data saved successfully!'],201);
        }
    }
    public function updateUsers(Request $request, $id){
        if($request->isMethod('put')){
          $data=$request->input();
          User::where('id',$id)->update(['name'=>$data['name'],'email'=>$data['email']]);
          return response()->json(['massage'=>'Data updated successfully!'],202);

        }
    }
    public function updateUserName(Request $request, $id){
       if($request->isMethod('patch')){
           $data=$request->input();
           User::where('id',$id)->update(['name'=>$data['name']]);
           return response()->json(['massage'=>'Name updated successfully!'],202);
       }
    }
    public function deleteUser(Request $request,$id){
        if($request->isMethod('delete')){
            User::where('id',$id)->delete();
           return response()->json(['massage'=>'Deleted successfully!'],202);
        }
    }
    public function deleteMultipleUser(Request $request,$ids){
        $ids=explode(",",$ids);
         User::whereIn('id',$ids)->delete();
        return response()->json(['massage'=>'Deleted successfully!'],202);
    }
    public function headerAuthorization(Request $request){
        $header=$request->header('Authorization');
        if(empty($header)){
          return response()->json(['massage'=>'Give token number!'],422);
        }else{
            $data= User::all();
            return response()->json(["users"=>$data]);
        }
    }
    public function registerUser(Request $request){
        if($request->isMethod('post')){
           $createToken = Str::random(60);
           $data=$request->input();
           $user = new User;
           $user->name=$data['name'];
           $user->email=$data['email'];
           $user->password=bcrypt($data['password']);
           $user->api_token=$createToken;
           $user->save();
           return response()->json(['massage'=>'Registered successfully!'],201);
        }
    }
    public function loginUser(Request $request){
            if($request->isMethod('post')){
                $data=$request->input();
                $rules=[
                'email'=>'required|email|unique:users',
                ];
                $customMassage=[
                    'email.required'=>"Fill email box is must",
                    'email.email'=>"Enter valid email",
                    'email.unique'=>"Email does not match"
                ];
                $validator=Validator::make($data, $rules, $customMassage);
                if($validator->fails()){
                 return response()->json($validator->errors(),422);
                }
              $userData=User::where('email',$data['email'])->first();
              if(password_verify($data['password'],$userData->password)){
                  $userLoginToken= Str::random(60);
                  User::where('email',$userData['email']->update('api_token',$userLoginToken));
                  return response()->json(['status'=>true, 'massage'=>"Logged in!",'token'=>$userLoginToken],201);
              }else{
                   return response()->json(['status'=>false, 'massage'=>"Password in incorrect!"],422);
              }

            }
    }

    public function registerUserWithPassport(Request $request){
        if($request->isMethod('post')){
             $data=$request->input();
                $rules=[
                'email'=>'required|email|unique:users',
                'password'=>'required',
                'name'=>'required',
                ];
                $customMassage=[
                    'email.required'=>"Fill email box is must",
                    'email.email'=>"Enter valid email",
                    'email.unique'=>"Email is already taken",
                    'password.required'=>"Give a password",
                    'name.required'=>"Give your name",
                ];
                $validator=Validator::make($data, $rules, $customMassage);
                if($validator->fails()){
                 return response()->json($validator->errors(),422);
                }
                $data=$request->input();
                $user = new User;
                $user->name=$data['name'];
                $user->email=$data['email'];
                $user->password=bcrypt($data['password']);
                $user->save();
                    if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
                        $user= User::where('email',$data['email'])->first();
                        // print_r(Auth::user());die;
                        // Generate passport token 
                        $accessToken= $user->createToken($data['email'])->accessToken;
                        User::where('email',$data['email'])->update(['api_token'=>$accessToken]);
                        return response()->json(['massage'=>'Registered successfully!','api_token'=>$accessToken],201);
                    }
                
                }
            }
}
