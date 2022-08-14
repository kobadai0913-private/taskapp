<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class PersonController extends Controller
{
    public function index(Request $request){
        //投稿をもつ・持たないを分ける  
        $hasItems = Person::has('boards')->get();
        $noItems = Person::doesntHave('boards')->get();
        $param = ['hasItems' => $hasItems, 'noItems'=>$noItems];
        return view('person.index', $param);
    }

    public function find(Request $request){
        return view('person.find',['input'=>'']);
    }

    // public function search(Request $request){
    //     $item = Person::find($request->input);
    //     $param = ['input'=>$request->input, 'item'=>$item];
    //     return view('person.find',$param);
    // }

    // public function search(Request $request){
    //     $item = Person::where('name',$request->input)->first();
    //     $param = ['input'=>$request->input, 'item'=>$item];
    //     return view('person.find',$param);
    // }

    // public function search(Request $request){
    //     $item = Person::nameEqual($request->input)->first();
    //     $param = ['input'=>$request->input, 'item'=>$item];
    //     return view('person.find',$param);
    // }

    public function search(Request $request){
        $min = $request->input * 1;
        $max = $min + 10;
        $item = Person::ageGreaterThan($min)->ageLessThan($max)->first();
        $param = ['input'=>$request->input, 'item'=>$item];
        return view('person.find', $param);
    }

    public function add(Request $request){
        return view('person.add');
    }

    public function create(Request $request){
        $this->validate($request, Person::$rules);
        //インスタンス作成
        $person = new Person;
        $form = $request->all();
        //テーブルにはないフィールドは削除しておく
        unset($form['_token']);
        //まとまったプロパティを個々のプロパティに保存し、インスタンスを保存する
        $person->fill($form)->save();
        return redirect('/person');
    }

    public function edit(Request $request){
        $person = Person::find($request->id);
        return view('person.edit', ['form'=>$person]);
    }

    public function update(Request $request){
        $this->validate($request, Person::$rules);
        $person = Person::find($request->id);
        $form = $request->all();
        //テーブルにはないフィールドは削除しておく
        unset($form['_token']);
        //まとまったプロパティを個々のプロパティに保存し、インスタンスを保存する
        $person->fill($form)->save();
        return redirect('/person');
    }

    public function delete(Request $request){
        $person = Person::find($request->id);
        return view('person.del',['form'=>$person]);
    }

    public function remove(Request $request){
        Person::find($request->id)->delete();
        return redirect('/person');
    }

    
}


