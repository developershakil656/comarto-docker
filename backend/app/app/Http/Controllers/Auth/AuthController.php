<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfileRequest;
use App\Http\Resources\AuthResource;
use App\Models\AccountVerification;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Verify OTP and login/register
    public function mobile_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:11',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails())
            return error_response('validation error!', $validator->errors());

        $otpRecord = Otp::where('number', $request->number)
            ->where('otp', $request->otp)
            ->latest()
            ->first();

        if (!$otpRecord || !$otpRecord->expires_at >= now()) {
            return error_response('Invalid or expired OTP.', [], 401);
        }

        // OTP is valid — delete old OTP for this number
        // Otp::where('number', $request->number)->delete();
        $otpRecord->delete();

        $user = User::where('number', $request->number)->first();
        if (!$user) {
            $user = User::create([
                'number' => $request->number,
                'email' => null,
            ]);
        }

        if ($user->status == 'blocked') {
            return error_response('Your account is blocked!', [], 403);
        }

        $token = $user->createToken('AccessToken', ['user'])->accessToken;
        return success_response('You are successfully logged in.', [
            'user' => new AuthResource($user),
            'token' => $token,
        ]);
    }

    public function google_login(Request $request)
    {
        try {
            // Step 1: Exchange authorization code for access token
            $tokenResponse = Socialite::driver('google')
                ->stateless()
                ->getAccessTokenResponse($request->code);

            $accessToken = $tokenResponse['access_token'];

            // Step 2: Retrieve user info using the access token
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($accessToken);

            if (!$googleUser || !$googleUser->getEmail()) {
                return error_response('Invalid Google token or missing email.', [], 401);
            }

            // Step 3: Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();

            // Step 4: If not, create a new user
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'profile' => $googleUser->getAvatar(),
                    'number' => null,
                    'status' => 'active',
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'password' => bcrypt(str()->random(16)), // optional for fallback
                ]);
            }
            // Step 5: Check if account is blocked
            if ($user->status === 'blocked') {
                return error_response('Your account is blocked!', [], 403);
            }

            // Step 6: Generate access token
            $token = $user->createToken('AccessToken', ['user'])->accessToken;

            // Step 7: Return success response
            return success_response('You are successfully logged in.', [
                'user' => new AuthResource($user),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return error_response('Google login failed.', ['error' => $e->getMessage()], 500);
        }
    }

    public function google_update(Request $request)
    {
        try {
            // Step 1: Exchange authorization code for access token
            $tokenResponse = Socialite::driver('google')
                ->stateless()
                ->getAccessTokenResponse($request->code);

            $accessToken = $tokenResponse['access_token'];

            // Step 2: Retrieve user info using the access token
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($accessToken);

            if (!$googleUser || !$googleUser->getEmail()) {
                return error_response('Invalid Google token or missing email.', [], 401);
            }

            $user = Auth::user();
            // Validate unique email excluding current user
            $validator = Validator::make(
                ['email' => $googleUser->getEmail()],
                [
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->ignore($user->id),
                    ],
                ]
            );

            if ($validator->fails()) {
                return error_response('This email is unavailable!', $validator->errors());
            }

            $user->update([
                'email' => $googleUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ]);
            return success_response('Email successfully updated');
        } catch (\Exception $e) {
            return error_response('Google login failed.', ['error' => $e->getMessage()], 500);
        }
    }

    public function send_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:11',
        ]);

        if ($validator->fails()) {
            return error_response('validation error!', $validator->errors());
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        // Fetch values from config
        $apiKey = config('services.bulksms.api_key');
        $senderId = config('services.bulksms.sender_id');
        $appName = config('app.name');

        // Construct the message with the frontend URL
        $message = "Your $appName verification code is: $otp. This code is valid for 5 minutes. please do not share this code with anyone.";

        try {
            $response = Http::get('http://bulksmsbd.net/api/smsapi', [
                'api_key'  => $apiKey,
                'type'     => 'text',
                'number'   => '88'.$request->number,
                'senderid' => $senderId,
                'message'  => $message,
            ]);

            $data = $response->json();
            $code = $data['response_code'] ?? null;

            if ($code == 202) {
                Otp::updateOrCreate(
                    ['number' => $request->number],
                    ['otp' => $otp, 'expires_at' => $expiresAt]
                );

                return success_response('OTP has been sent successfully.', ['otp' => $otp]);
            }

            // Handle specific error codes from your list
            $errorMessage = match ($code) {
                1001 => 'The phone number provided is invalid.',
                1007 => 'SMS gateway balance is insufficient.',
                1032 => 'Server IP not whitelisted. Please contact admin.',
                default => $data['error_message'] ?? 'SMS provider error occurred.',
            };

            return error_response($errorMessage, ['vendor_code' => $code]);
        } catch (\Exception $e) {
            Log::error("BulkSMSBD Critical Error: " . $e->getMessage());
            return error_response('Unable to connect to SMS gateway.');
        }
    }

    #for check business number or login
    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:11',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails())
            return error_response('validation error!', $validator->errors());

        $otpRecord = Otp::where('number', $request->number)
            ->where('otp', $request->otp)
            ->latest()
            ->first();

        if (!$otpRecord || $otpRecord->expires_at < now()) {
            return error_response('Invalid or expired OTP.', [], 401);
        }

        // OTP is valid — delete old OTP for this number
        // Otp::where('number', $request->number)->delete();
        $otpRecord->delete();

        $user = auth('api')->user();
        if ($user && $request->profile_update) {
            $validator = Validator::make($request->all(), [
                'number' => [
                    'required',
                    Rule::unique('users', 'number')->ignore($user->id),
                ],
            ]);

            if ($validator->fails()) {
                return error_response('This number is unavailable!', $validator->errors());
            }

            $user->number = $request->number;
            $user->save();
        }

        return success_response('You are ready to go!');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return success_response('You are successfully logged out.');
    }

    // All personal information read and update ------------------------------------------------------

    public function details()
    {
        $user = Auth::user();

        return success_response('products fetched.', new AuthResource($user));
    }

    public function name_update(UserProfileRequest $request)
    {
        $data = Auth::user();
        if ($data->update($request->only('name')))
            return success_response('name successfully updated', new AuthResource($data));
        else
            return error_response('No Data Found!');
    }

    #user profile picture update
    public function profile_update(Request $request)
    {
        // return $request->all();
        // Validate the request: Ensure the 'profile' is a valid image file (jpg, jpeg, png)
        $validator = Validator::make($request->all(), [
            'profile' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120'  // Max size: 5MB (can be adjusted)
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
        $image = image_link_generator($request->file('profile'), 'user/profile/', $user->name, 220);

        // Update the user's profile with the new image URL
        $user->profile = $image;
        $user->save();

        // Save the updated user data
        // if ($user->save()) {
        //     // Resize and save the image
        //     Image::make($request->file('profile'))  // Get the uploaded file
        //         ->resize(150, 150)  // Resize to 150x150
        //         ->save(public_path($image));  // Save it in the specified path
        // }
        // Return success response with updated user profile
        return success_response('Profile picture successfully updated', new AuthResource($user));
    }

    #account verificaion
    public function account_verification(Request $request)
    {
        // Validate the request: Ensure the 'nid_front' and 'nid_back' are valid image files
        $validator = Validator::make($request->all(), [
            'nid_number' => ['required', 'regex:/^\d{13}$|^\d{17}$/'],
            'nid_front' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',  // Image validation for front side
            'nid_back' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',   // Image validation for back side
        ]);

        // Return validation error if validation fails
        if ($validator->fails()) {
            return error_response('Validation error!', $validator->errors());
        }

        // Retrieve the existing verification record (if any)
        $verification = AccountVerification::where('user_id', Auth::id())->first();

        // Delete the previous images if they exist
        if ($verification) {
            if ($verification->nid_front) {
                delete_image($verification->nid_front); // Delete old front image
            }
            if ($verification->nid_back) {
                delete_image($verification->nid_back);  // Delete old back image
            }
        }

        // Store new images using the image_link_generator function
        $nid_front = image_link_generator($request->file('nid_front'), 'user/nid/', $request->nid_number, 0, false);
        $nid_back = image_link_generator($request->file('nid_back'), 'user/nid/', $request->nid_number, 0, false);

        // Update or create verification record
        $verification = AccountVerification::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'nid_number' => $request->nid_number,
                'nid_front'  => $nid_front,
                'nid_back'   => $nid_back,
                'status'   => 'in review',
            ]
        );

        // Return success response
        return success_response('Your account verification is in review.');
    }


    #user location update
    public function location_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'post_code' => 'required|digits:4',
            'location_id' => 'required|string',
        ]);
        if ($validator->fails())
            return error_response('validation error!', $validator->errors());

        $location_id = $request->location_id ? Hashids::decode($request->location_id) : [];
        $request['location_id'] = $location_id[0];
        $user = Auth::user();
        if ($user->update($request->only('address', 'location_id', 'post_code')))
            return success_response('location successfully updated', new AuthResource($user));
        else
            return error_response('No user found!');
    }
}
