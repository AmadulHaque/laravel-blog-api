<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PhoneMailExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => ['required','regex:/^01[3-9]\d{8}$/','unique:users'],
                'address' => 'required|string|max:200',
                'password' => 'required|string|min:6',
            ]);

            $data['password'] = Hash::make($request->password);

            User::create($data);

            return successResponse('User registration in successfully',[],201);

        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return errorResponse('Validation failed', $errors, 422);
        } catch (\Exception $e) {
            return errorResponse('An error occurred during register.', [$e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => ['required'],
                'password'   => [
                    'required',
                    function ($attribute, $value, $fail) use ($request, &$user) {
                        // Find the user by phone or email
                        $user = User::where('email', $request->email)->first();
                        // Check if user exists and the password matches
                        if (!$user || !Hash::check($value, $user->password)) {
                            $user = null; // Reset user if authentication fails
                            $fail('Invalid password');
                        }
                    },
                ],
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                // $token = $user->createToken('Personal Access Token')->plainTextToken;
                $token = $user->createToken('MyApp')->accessToken;

                return successResponse('User login in successfully',['token' => $token],200);

            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return errorResponse('Validation failed', $errors, 422);
        } catch (\Exception $e) {
            return errorResponse('An error occurred during login.', [$e->getMessage()], 500);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return successResponse('Logged out successfully',[],200);
    }

    public function forgotPassword(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|string|email',
            ]);



            return successResponse('Password reset link sent',[],200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return errorResponse('Validation failed', $errors, 422);
        } catch (\Exception $e) {
            return errorResponse('An error occurred during forgotPassword.', [$e->getMessage()], 500);
        }

    }


}
