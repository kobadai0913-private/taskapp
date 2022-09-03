<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use Illuminate\Support\Facades\Log;

class InformationController extends Controller
{
    //インフォメーション登録画面
    public function information_insert(){
        //インフォメーション登録画面に遷移
        return view('information.information_insert');
    }

    //インフォメーション登録処理
    public function information_insert_registration(Request $request){
        //バリデーション処理
        $rules = [
            'information_date' => 'required|after:yesterday',
            'information_detail' => 'required',
            'information_name' => 'required',
        ];
    
        $messages=[
                'information_name.required' => 'インフォメーション名は必ず入力して下さい。',
                'information_detail.required' => 'インフォメーション詳細は必ず入力して下さい。',
                'information_date.required' => 'インフォメーション日付は必ず入力して下さい。',
                'information_date.after' => 'インフォメーション日付には今日以降の日付を入力して下さい。',
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);

        //エラー処理
        $request->session()->flash('information_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/information/add')
            ->withErrors($validator)
            ->withInput();
        }

        $maxId = DB::table('information_board')
            ->select('information_id')
            ->orderBy('information_id','desc')
            ->first();
        if($maxId==null){
            $information_id = 1;
        }else{
            $information_id = $maxId->information_id+1;
        }
        $param = [
            'information_id' => $information_id,
            'information_name' => $request->information_name,
            'information_detail' => $request->information_detail,
            'information_date' => $request->information_date,
        ];

        DB::insert('insert into information_board(information_id, information_name, information_date, information_detail) values(:information_id, :information_name, :information_date, :information_detail)',$param);
        $request->session()->flash('informationinsert_message', 'インフォメーションを追加しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //インフォメーション修正(get)
    public function information_fix(Request $request){
        $param = [
            "information_id" => $request->information_id,
        ];
        $items = DB::select('select information_id, information_detail, information_date, information_name from information_board where information_id = :information_id',$param);
        
        //タスク修正画面に遷移
        return view('information.information_fix',['informations'=>$items]);
    }

    //インフォメーション修正(post)
    public function information_fix_registration(Request $request){
        //バリデーション処理
        $rules = [
            'information_date' => 'required|after:yesterday',
            'information_detail' => 'required',
            'information_name' => 'required',
        ];
    
        $messages=[
            'information_name.required' => 'インフォメーション名は必ず入力して下さい。',
            'information_detail.required' => 'インフォメーション詳細は必ず入力して下さい。',
            'information_date.required' => 'インフォメーション日付は必ず入力して下さい。',
            'information_date.after' => 'インフォメーション日付には今日以降の日付を入力して下さい。',
    ];
        $validator  = Validator::make($request->all(), $rules, $messages);

        //エラー処理
        $request->session()->flash('information_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/information/fix/'.$request->information_id)
            ->withErrors($validator)
            ->withInput();
        }

        $param = [
            'information_id' => $request->information_id,
            'information_name' => $request->information_name,
            'information_date' => $request->information_date,
            'information_detail' => $request->information_detail,
        ];

        DB::update('update information_board set information_date = :information_date, information_detail = :information_detail, information_name = :information_name where information_id = :information_id',$param);
        $request->session()->flash('informationupdate_message', 'インフォメーションを更新しました');
        
        //タスク一覧に画面遷移する
        return redirect('task/app');
    }

    //インフォメーション削除
    public function information_delete(Request $request){
        $param = [
            'information_id' => $request->information_id,
        ];
        //指定したタスクID以上のタスクIDを昇順抽出
        $information_ids = DB::select('select information_id from information_board where information_id > :information_id order by information_id asc',$param);
        DB::delete('delete from information_board where information_id = :information_id',$param);
        foreach($information_ids as $information_id){
            $param = [
                'information_id' => $information_id->information_id,
            ];
            DB::update('update information_board set information_id = :information_id - 1 where information_id = :information_id ',$param);
        }
        $request->session()->flash('informationdelete_message', 'インフォメーションを削除しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //インフォメーション詳細(get)
    public function information_detail(Request $request){
        $param = [
            "information_id" => $request->information_id,
        ];
        $items = DB::select('select information_name, information_detail, information_date from information_board where information_id = :information_id',$param);
        
        //タスク詳細画面に遷移
        return view('information.information_detail',['informations'=>$items]);
    }
}
