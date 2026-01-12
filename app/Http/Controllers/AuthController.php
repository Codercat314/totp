<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Storage\UserRepository;
use App\Services\totpService;
use App\Services\jwtService;
use Symfony\Component\HttpFoundation\Cookie;
use Carbon\Carbon;

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
        $expiresAt = Carbon::now()->addDays(30)->format("Y-m-d H:i:s");

        //spara refreshtoken
        $this->repo->saveRefreshToken($user->id, $refreshToken, $expiresAt);


        $cookie=Cookie::create(
            'refresh_token',
            $refreshToken,
            $expiresAt,
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

    public function refresh(Request $request){
        $refreshToken=$request->cookie('refresh_token');

        if(!$refreshToken){
            return response()->json(['error'=>'missing refreshtoken'], 401);
        }

        $user=$this->repo->getUserByRefreshToken($refreshToken);
        if(!$user){
            return response()->json(['error'=>'Invalid refreshtoken (no user)']);
        }

        //skapa nya tokens för användaren
        $accessToken=$this->jwtService->createAccessToken($user->id);
        $newRefreshToken=$this->jwtService->createRefreshToken();
        $this->repo->saveRefreshToken($user->id, $newRefreshToken);
        $expiresAt = Carbon::now()->addDays(30)->format("Y-m-d H:i:s");


        $cookie=Cookie::create(
            'refresh_token',
            $newRefreshToken,
            $expiresAt,
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
    public function logout(Request $request){
        $refreshToken = $request->cookie('refresh_token');

        if ($refreshToken) {
            $this->repo->deleteRefreshToken($refreshToken);
        }

        $cookie=Cookie::create(
            'refresh_token',
            null,
            -1,
            'refresh',
            null,
            true,
            true,
            false,
            'lax'
        );

        //Retunera ett ogiltigt accesstoken
        return response()->json([
            'access_token'=>null,
            'expires'=>-1
        ], 204)->withoutCookie($cookie);
    }
}