<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use Illuminate\Support\Facades\Log;

use App\Model\Information;
use App\Model\User;

class InformationController extends Controller
{
    //インフォメーション登録画面
    public function information_insert(){
        //インフォメーション登録画面に遷移
        return view('information.information_insert');
    }

    //インフォメーション登録処理
    public function information_insert_registration(Request $request){

        //バリデーション情報取得
        $rules = Information::$rules;
        $messages = information::$messages;

        //入力情報取得
        $information_name = $request->information_name;
        $information_detail = $request->information_detail;
        $information_date = $request->information_date;

        //変数定義
        $work_max_id;
        $information_id;

        //バリデーション処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        $request->session()->flash('information_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/information/add')
            ->withErrors($validator)
            ->withInput();
        }

        $work_max_id = Information::select('information_id')
                                    ->orderby('information_id','desc')
                                    ->first()
                                    ->get();
        if($work_max_id==null){
            $information_id = 1;
        }else{
            $information_id = $work_max_id[0]->information_id+1;
        }

        //insertパラメータ取得
        $insert_param = Information::$information_param;

        //パラメータセット
        $insert_param['information_id']=$information_id;
        $insert_param['information_name']=$information_name;
        $insert_param['information_detail']=$information_detail;
        $insert_param['information_date']=$information_date;

        //パラメータinsert処理
        Information::insert($information_param);

        $request->session()->flash('informationinsert_message', 'インフォメーションを追加しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //インフォメーション修正(get)
    public function information_fix(Request $request){

        //インフォメーション情報取得
        $items = Information::select('information_id','information_detail','information_date','information_name')
                                ->where('information_id',$information_id)
                                ->get();

        //インフォメーション詳細修正画面に遷移
        return view('information.information_fix',['informations'=>$items]);
    }

    //インフォメーション修正(post)
    public function information_fix_registration(Request $request){

        //入力情報取得
        $information_id = $request->information_id;
        $information_name = $request->information_name;
        $information_date = $request->information_date;
        $information_detail = $request->information_detail;

        //バリデーション情報取得
        $rules = Information::$rules;
        $messages = Information::$messages;

        //エラー処理
        $request->session()->flash('information_errors', '入力項目に問題があります。');
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/information/fix/'.$request->information_id)
            ->withErrors($validator)
            ->withInput();
        }

        //パラメータ取得
        $update_param = Information::$information_param;

        //パラメータセット
        $update_param['information_id']=$information_id;
        $update_param['information_name']=$information_name;
        $update_param['information_date']=$information_date;
        $update_param['information_detail']=$information_detail;

        //インフォメーション情報更新
        Information::update($update_param);

        $request->session()->flash('informationupdate_message', 'インフォメーションを更新しました');
        
        //インフォメーション詳細に画面遷移する
        return redirect('information/detail/'.$information_id);
    }

    //インフォメーション削除
    public function information_delete(Request $request){

        //インフォメーションID取得
        $information_id = $request->information_id;

        //指定したタスクID以上のタスクIDを昇順抽出
        $information_datas = Information::select('information_id')
                                            ->where('information','>',$information_id)
                                            ->get();
        
        //インフォメーション情報削除
        Information::where('information_id',$information_id)
                        ->delete();

        //インフォメーションID更新        
        foreach($information_datas as $information_data){
            $update_param = [
                'information_id' => ($information_data->information_id-1),
            ];
            Information::where('information_id',$information_id)
                            ->update($update_param);
        }

        $request->session()->flash('informationdelete_message', 'インフォメーションを削除しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //インフォメーション詳細(get)
    public function information_detail(Request $request){

        //ログインユーザID取得
        $login_user = $request->session()->get('user_id');

        //変数定義
        $user_name;
        
        //ユーザ名取得
        $user_data = User::select('user_name')
                            ->where('user_id',$login_user)
                            ->get();
        $user_name = $user_data[0]->user_name;
        
        //インフォメーションカラム更新
        $update_sql = 'update information_board set '.str($user_name).'_flg = true where information_id = '.$request->information_id;
        DB::update($update_sql);
        
        //インフォメーション情報取得
        $items = Information::select('information_id', 'information_name', 'information_detail', 'information_date')
                                ->where('information_id',$information_id)
                                ->get();

        //タスク詳細画面に遷移
        return view('information.information_detail',['informations'=>$items]);
    }
}
