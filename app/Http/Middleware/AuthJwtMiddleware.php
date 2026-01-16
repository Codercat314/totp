<?php

namespace App\Http\Middleware;

use App\Services\jwtService;
use Illuminate\Http\Request;


class AuthJwtMiddleware {
    public function __construct(private JwtService $jwtService){

    }

    public function handle(Request $request, \Closure $next) {
        
        $authHeader = $request->header('Authorization');
        //dd($authHeader);

        if(!$authHeader || !str_starts_with($authHeader, 'Bearer')){
            return response()->json(['Error'=>'Missing (or invalid) token'], 401);
            }
            
            $token=substr($authHeader, 7);
            
            
                $payload=$this->jwtService->validate($token);
                if(!$payload){
                    
                    return response()->json(['Error'=>'(Missing or) invalid token'], 401);
                }
               
            $request->attributes->set('jwt_payload', $payload);

            $request->setUserResolver(function() use($payload){
                return (object) ['id'=>$payload->sub, 
                'email'=>$payload->email ?? null];
            });

            return $next($request);
    }
}