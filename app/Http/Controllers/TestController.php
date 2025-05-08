<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
class TestController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/test",
     *     tags={"Test"},
     *     summary="Returns a test response",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function index(){
        return response()->json([
            'name' => 'Masud',
            'email' => 'masud98.cse@gmail.com',
        ]);
    }
}
