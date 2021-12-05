<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\User;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('jwt.verify',['except' => ['login','register']]);
    }

    public function login(Request $request){
        $validator =  Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $token_validity  = 1*60;
        $this->guard()->factory()->setTTL($token_validity);
        $credentials = $request->only("email", "password");
        
        // if(!$token = auth()->attempt($validator->validated())){
        if(! $token = auth('api')->attempt($credentials)){
            return response()->json(['error' => 'Unauthorized'],401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|regex:/^[789]\d{9}$/|min:10|unique:users',
            'email' => 'required|email|unique:users',
            'age' => 'required|integer|between:1,127',
            'gender' => ['required',Rule::in(['m', 'f', 'o'])],
            'city' => 'required|string',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json(['message' => 'User created successfully','user' => $user]);
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['message' => 'User logged out successfully']);
    }

    public function refresh(){
        return $this->respondWithToken(auth('api')->refresh());
    }

    public function profile(){
        return response()->json(auth('api')->user());
    }

    public function update(Request $request){
        $loggeInUser = $this->guard()->user();
        $validator = Validator::make($request->all(),[
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|regex:/^[789]\d{9}$/|min:10|unique:users,u_id,'.$loggeInUser->u_id,
            'email' => 'required|email|unique:users,u_id,'.$loggeInUser->u_id,
            'age' => 'required|integer|between:1,127',
            'gender' => ['required',Rule::in(['m', 'f', 'o'])],
            'city' => 'required|string',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $user = User::find($loggeInUser->u_id);
            
        if($loggeInUser->email != 'admin@gmail.com'){
            
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->age = $request->age;
            $user->city = $request->city;
            $user->password = $request->password;

            if($user->update()){
                return response()->json(['message' => 'successfully updated.']);
   
            }else{
                return response()->json(['message' => 'something went wrong.']);
            }
        }else{
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mobile = $request->mobile;
            $user->age = $request->age;
            $user->city = $request->city;
            $user->password = $request->password;

            if($user->update()){
                return response()->json(['message' => 'Data successfully updated except email.']);
   
            }else{
                return response()->json(['message' => 'something went wrong.']);
            }
        }
        
    }

    public function destroy($id){   
        $loggedInUser =  $this->guard()->user();
        $deleteUser = User::find($id);
        if($loggedInUser->email == 'admin@gmail.com'){
            if($deleteUser->email == 'admin@gmail.com'){
                return response()->json(['message' => 'admin user can not be delete.']);
            }else{
                if(!empty($deleteUser)){
                    $deleteUser->delete();
                    return response()->json(['message' => 'Succesfully deleted.']);
                }else{
                    return response()->json(['message' => 'Something went wrong.']);
                }
            }
                
        }else{
            return response()->json(['message' => 'You do not have a delete permission.'],403);
        }
    }

    public function books(){
        $book = $this->guard()->user()->book;
        return response()->json($book);
    }

    protected function respondWithToken($token){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
            'user' => Auth::user(),
        ]);
    }

    protected function guard(){
        return Auth::guard();
    }
}
