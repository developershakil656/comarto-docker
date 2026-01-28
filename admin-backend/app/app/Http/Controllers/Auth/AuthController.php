<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    #admin login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) 
            return error_response('validation error!',$validator->errors());

        $credentials = $request->only('email','password');
        if (Auth::attempt($credentials)) {
            // config(['auth.current.provider' => 'admin']);

            $admin          = new AuthResource(Auth::user());
            $data['admin']  = $admin;
            $data['token'] = $admin->createToken('AccessToken',['admin'])->accessToken;

            return success_response('You are successfully logged in.',$data);
        } else {
            return error_response('email or password are incorrect!',[], 401);
        }
    }

    #admin logout
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return success_response('You are successfully logged out.');
    }

    #admin profile details update
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required|digits:11'
        ]);

        if ($validator->fails()) 
            return error_response('validation error!',$validator->errors());

        $admin = Auth::user();
        $admin->name = $request->name;
        $admin->number = $request->number;
        
        $admin->save();

        return success_response('profile details successfully updated',new AuthResource($admin));
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) 
            return error_response('validation error!',$validator->errors());

        $admin = Auth::user();
        if(Hash::check($request->old_password, $admin->password)){
            $admin->password = Hash::make($request->new_password);
            $admin->save();
            return success_response('password successfully changed');
        }else{
            return error_response('validation error!',["old_password" => ["old password doesn't matched."]]);
        }
    }

    #admin profile picture update
    // public function profile(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'profile' => 'required',
    //     ]);
    //     if ($validator->fails()) 
    //         return error_response('validation error!',$validator->errors());

    //     $admin = Auth::user();
    //     #remove exist image
    //     if(file_exists($admin->profile))
    //             unlink($admin->profile);
    
    //     #store new image
    //     $image = image_link_genterator($request->profile, Auth::user()->name, Auth::user()->id, 'gallery/admin/profile/');
    //     $admin->profile = $image['file_url'];

    //     if($admin->save()){
    //         Image::read(base64_decode($image['image']))->resize(150, 150)->save($image['file_url']);
    //     }
        
    //     return success_response('profile picture successfully updated',new AuthResource($admin));

    // }
    public function profile(Request $request)
    {
        // Validate the request: Ensure the 'profile' is a valid image file (jpg, jpeg, png)
        $validator = Validator::make($request->all(), [
            'profile' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'  // Max size: 2MB (can be adjusted)
        ]);

        // Return validation error if validation fails
        if ($validator->fails()) {
            return error_response('Validation error!', $validator->errors());
        }

        $user = Auth::user();
        // Remove the existing profile image if it's not a URL and the file exists
        if ((!Str::startsWith($user->profile, 'http://') && !Str::startsWith($user->profile, 'https://'))) {
            delete_image($user->profile);
        }

        // Store the new image using the image_link_generator function
        $image = image_link_generator($request->file('profile'), '/admin/profile/', $user->name);

        // Update the user's profile with the new image URL
        $user->profile = $image;
        $user->save();

        return success_response('Profile picture successfully updated', new AuthResource($user));
    }

}
