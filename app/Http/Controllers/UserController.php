<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Todo;

class UserController extends Controller
{
    public function index (Request $request) {
//        $data = $request;
//        return $data;
        $user = JWTAuth::parseToken()->authenticate();

        $todo = Todo::where('user_id', '=', $user->id)->get();
        return response()->json(compact('todo'));
    }

    public function store (Request $request){
        //1. 유저 인증
        //2. 데이터 마이닝
        //3. 결과 리턴
//        dd($request->all());
        $user = JWTAuth::parseToken()->authenticate();
        $user or die('invalid_authorization');

        if($request->id){
            $todo = Todo::where('id','=',$request->id)->first();
            //왜 get으로 하면 save에 오류가 나고, first로 하면 오류가 없지??
            //get 은 값이 하나이더라도 object가 아니라 collection이기 때문에.
        } else {
            $todo = new Todo();
        }

        $todo->title = $request->title;
        $todo->date = $request->date;
        $todo->type = $request->type;
        $todo->user_id = $user->id;

        $todo->save();

        return response()->json(compact('todo'));
    }

    public function destroy(Request $request){
//        return $request->all();
        $todo = Todo::findOrFail($request->id);
        $todo->delete();

        return response()->json(['success' => 1]);
    }
}

