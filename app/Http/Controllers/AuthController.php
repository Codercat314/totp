<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Storage\UserRepository;
use App\Services\totpService;
use App\Services\jwtService;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller{
    public function __construct(private UserRepository $repo, private totpService $totpService, private jwtService $jwtService){

    }

    public function login(Request $request){
        $email=filter_var($request->input('email'), FILTER_VALIDATE_EMAIL);
        $code=filter_var($request->input('code'), FILTER_VALIDATE_INT);
        

        $user=$this->repo->getUserByEmail($email);
        if (!$user) {
            return response()->json(['error'=>'invalid credentials'], 401);
        }
        //verifiera kod
        $totpValid=$this->totpService->verify($user->secret, $code);
        
        if (!$totpValid) {
            return response()->json(['error'=>'invalid credentials'], 401);
        }

        $accessToken=$this->jwtService->createAccessToken($user->id);
        $refreshToken=$this->jwtService->createRefreshToken();

        $cookie=Cookie::create(
            'refresh_token',
            $refreshToken,
            60*60*24*30,
            'refresh',
            null,
            true,
            true,
            false,
            'lax'
        );

        return response()->json([
            'access_token'=>$accessToken,
            'token_type'=>'Bearer',
            'expires_in'=>900,
            'user'=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email
            ]
        ])->withCookie($cookie);
       
    }
}