<?php

namespace As247\Puller;

use Illuminate\Http\Request;

class PullerController
{
    function messages(Request $request, PullerManager $pullerManager){
        $channel=$request->input('channel');
        $token=$request->input('token');
        $isPrivate=strpos($channel, 'private-')===0;
        if(!$channel){
            return response()->json(['error'=>'channel is required'],400);
        }
        if(!$isPrivate){
            if(!$token){
                $token=$pullerManager->getToken($channel);
            }
        }
        try {
            $messages = $pullerManager->fetch($channel, $token);
            return response()->json($messages);
        }catch (\Exception $exception){
            return response()->json(['error'=>$exception->getMessage()],400);
        }
    }
}
