<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(Request $request){
        $items = Board::with('person')->get();
        return view('board.index',['items'=>$items]);
    }

    public function add(Request $request){
        return view('board.add');
    }

    public function create(Request $request){
        $this->validate($request, Board::$rules);
        //インスタンス作成
        $board = new Board;
        $form = $request->all();
        //テーブルにはないフィールドは削除しておく
        unset($form['_token']);
        //まとまったプロパティを個々のプロパティに保存し、インスタンスを保存する
        $board->fill($form)->save();
        return redirect('/board');
    }
}
