<?php

namespace As247\Puller;

use As247\Puller\Exceptions\InvalidTokenException;
use Illuminate\Http\Request;

class PullerController
{
    function messages(Request $request, PullerManager $pullerManager){
        set_time_limit(3600);
        $channel=$request->input('channel');
        $token=$request->input('token');
        $isPrivate=strpos($channel, 'private-')===0;
        if(!$channel){
            return response()->json(['error'=>'channel is required'],400);
        }
        $isNewToken=false;
        if(!$isPrivate){
            if(!$token){
                $token=$pullerManager->getToken($channel);
                $isNewToken=true;
            }
        }
        try {
            do{
                $messages = $pullerManager->pull($channel, $token);
                if($message=$messages->last()){
                    $token=$message->token;
                }
                $messages=$messages->map(function ($message){
                    return $message->payload;
                });
                if($isNewToken || !$messages->isEmpty() || connection_aborted()){
                    break;
                }
                sleep(1);

            }while(1);
            return response()->json(['messages' => $messages,'token'=>$token], 200);
        }catch (InvalidTokenException $exception){
            return response()->json(['error'=>$exception->getMessage()],400);
        }
    }
}
