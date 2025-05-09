<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="masud"),
     *             @OA\Property(property="email", type="string", format="email", example="masudcse@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration Successful"),
     *             @OA\Property(property="user", type="object",ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string", example="your-jwt-token-here"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expire_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function register(Request $request){
        $validation = Validator::make($request->all(),[
            'name'     => 'required|max:255|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|',
            'role'     => 'required|in:admin,user'
        ]);

        if($validation->fails()){
            return response()->json($validation->errors(),422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registration Successful',
            'user' => new UserResource($user),
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
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get paginated list of users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function users()  {
        return UserResource::collection(User::paginate(2));
    }
}
