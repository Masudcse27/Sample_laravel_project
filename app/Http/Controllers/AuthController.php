<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     )
     * )
     */
    public function register(Request $request){
        $validation = Validator::make($request->all(),[
            'name' => 'required|max:255|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|'
        ]);

        if($validation->fails()){
            return response()->json($validation->errors(),422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registration Successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => auth()->factory()->getTTL()*60
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if(!$token = JWTAuth::attempt($credentials)){
            return response()->json(['message'=>'Credential not match'], 400);
        }
        
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expire_in'=>auth()->factory()->getTTL()*60
        ], 200);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message'=>'Logout Successful'],200);
    }
}
