<?php

//Controllers 名前空間
//名前空間:クラスを階層的に整理するための仕組み
namespace App\Http\Controllers;

//useによるクラスのインポート
//requestを使用できる状態にしている
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\HelloRequest;
use Validator;
use Illuminate\Support\Facades\DB;


// //クラスの定義
// class HelloController extends Controller
// {
//     //アクションの追加
//     //引数を持たないメソッドをして用意されます。
//     public function index(){

//         //htmlのソースコードを返している
//         return <<<EOF

//     <html>
//     <head>
//     <title>Hello/Index</title>
//     <style>
//     body {font-size:16pt; color:#999; }
//     h1 { font-size:100px; text-align:right; color:#eee;
//         margin:-40px 0px -50px 0px; }
//     </style>
//     </head>
//     <body>
//         <h1>index</h1>
//         <p>これは、Helloコントローラのindexアクションです。</p>
//     </body>
//     </html>
//     EOF;
        
//      }
// }

// //クラスの定義(ルートパラメータの利用)
// class HelloController extends Controller
// {
//     //アクションの追加
//     //引数を持たないメソッドをして用意されます。
//     //$id,$passがルートパラメータで指定した値になる
//     public function index($id='noname', $pass='unkown'){

//         //htmlのソースコードを返している
//         return <<<EOF

//     <html>
//     <head>
//     <title>Hello/Index</title>
//     <style>
//     body {font-size:16pt; color:#999; }
//     h1 { font-size:100px; text-align:right; color:#eee;
//         margin:-40px 0px -50px 0px; }
//     </style>
//     </head>
//     <body>
//         <h1>index</h1>
//         <p>これは、Helloコントローラのindexアクションです。</p>
//         <ul>
//             <li>ID: {$id}</li>
//             <li>ID: {$pass}</li>
//         </ul>
//     </body>
//     </html>
//     EOF;
        
//      }
// }

// //複数アクションの利用
// //グローバル変数の定義
// global $head, $style, $body, $end;
// $head = '<html><head>';
// $style = <<<EOF
// <style>
// body {font-size:16pt; color:#999; }
// h1 { font-size:100pt; text-align:right; color:#eee; 
//     margin:-40px 0px -50px 0px; }
// </style>
// EOF;

// $body = '</head><body>';
// $end = '</body></html>';

// function tag($tag, $txt){
//     return "<$tag>".$txt."</{$tag}>";
// }


// //コントローラの定義
// class HelloController extends Controller
// {
//     //index関数の定義
//     public function index(){
//         global $head, $style, $body, $end;

//         //右上のでっかい文字
//         $html = $head . tag('title', 'Hello/Index') . $style .
//                 $body
//                 //this is a pageの文字
//                 .tag('h1','Index').tag('p', 'this is index page')
//                 //リンク
//                 .'<a href="/hello/other">go to Other page</a>'
//                 . $end;
//         return $html;
//     }

//     //other関数の定義
//     public function other(){
//         global $head, $style, $body, $end;

//         //右上のでっかい文字
//         $html = $head . tag('title', 'Hello/Other') . $style .
//                 $body
//         //this is a pageの文字
//                 . tag('h1','Other').tag('p', 'this is Other page')
//                 . $end;
//         return $html;
//     }
// }

//シングルアクションコントローラの定義
//1つのコントローラに1つのアクションだけしか用意しない
// class HelloController extends Controller
// {
//     public function __invoke(){
        
//         return <<<EOF
//         <html>
//         <head>
//         <title>Hello</title>
//         <style>
//         body {font-size:16pt; color:#999; }
//         h1 { font-size:30px; text-align:right; color:#eee;
//              margin:-15px 0px 0px 0px; }
//         </style>
//         </head>
//         <body>
//             <h1>Single Action</h1>
//             <p>これは、シングルアクションコントローラのアクションです。</p>
//         </body>
//         </html>
//         EOF;
//     }
// }

class HelloController extends Controller
{

    //request,responseはデフォルトで設定している変数を利用している
    //Requestの主なメソッド
    //アクセスしたアドレスを返す(クエリー文字列は省略される(アドレスの後につけられる?abc=xyzのような文字列))
    //$request->url();
    //アクセスしたアドレスを完全な形で返す(クエリー文字列も含める)
    //$request->fullUrl();
    //アクセスしたドメインの下のパス部分だけ返す
    //$request->path();
    //アクセスに関するステータスコードを返す(正常にアクセスが終了していたら200を返す)
    //$this->status();
    //コンテンツの取得、設定を行うもの
    //$this->content()
    //$this->setContent(値);

    // public function index(Request $request, Response $response){

    //     $html = <<<EOF
    //     <html>
    //     <head>
    //     <title>Hello/Index</title>
    //     <style>
    //     body { font-size:16px; color:#999; }
    //     h1 { font-size:120px; text-align:right; color:#fafafa;
    //         margin:-50px 0px -120px 0px; }
    //     </style>
    //     </head>
    //     <body>
    //         <h1>Hello</h1>
    //         <h3>Request</h3>
    //         <pre>{$request}</pre>
    //         <h3>Response</h3>
    //         <pre>{$response}</pre>
    //     </body>
    //     </html>
    //     EOF;
            
    //         $response->setContent($html);
    //         return $response;
    // }

    //テンプレートをコントローラから呼ぶ
    // public function index(){
    //     return view('hello.index');
    // }

    // //テンプレート空コントローラを呼ぶ(2)
    // public function index(){
    //     $data = ['msg'=>'これはコントローラから渡されたメッセージです。'];
    //     return view('hello.index', $data);
    // }

    //ルートパラメータをテンプレートに返す
    // public function index($id = 'zero'){
    //     $data = ['msg'=>'これはコントローラから渡されたメッセージです。',
    //              'id'=>$id
    //             ];
    //     return view('hello.index', $data);
    // }

    //クエリー文字の利用
    // public function index(Request $request){

    //     $data = ['msg'=>'これはコントローラから渡されたメッセージです。',
    //             //idというキーにアドレスバーで設定したテキストを設定してアクセスしている。
    //             //クエリー文字列を使って渡された値が$request->キー名に設定される。
    //              'id'=>$request->id
    //             ];
    //     return view('hello.index', $data);
    // }

    //indexアクションメソッドの定義
    // public function index(){
    //     $data = [
    //         'msg'=>'これはBladeを利用したサンプルです。',
    //     ];
    //     return view('hello.index', $data);
    // }

    // //indexアクションメソッドの定義
    // public function index(){
    //     $data = [
    //         'msg'=>'お名前を入力してください。',
    //     ];
    //     return view('hello.index', $data);
    // }

    // //アクションの追加
    // public function post(Request $request){
    //     //requestインスタンスを引数に用意している。
    //     //name="msh"を指定してあったフィールドの値は、このように$request->msgで取り出すことが出来る
    //     //フォームで送信された値は、全てnameのプロパティにとして取り出せるようになっている        
    //     $msg = $request->msg;
    //     $data = [
    //         'msg'=>'こんにちは、'.$msg.'さん!',
    //     ];
    //     return view('hello.index', $data);
    // }

    // public function index(){
    //     return view('hello.index', ['msg'=>'']);
    // }

    // public function post(Request $request){
    //     return view('hello.index', ['msg'=>$request->msg]);
    // }

    // public function index(){
    //     return view('hello.index');
    // }

    // public function post(Request $request){
        
    //     return view('hello.index', ['msg'=>$request->msg]);
    // }

    // public function index(){


    //     $data = ['one', 'two', 'three', 'four', 'five'];
    //     return view('hello.index', ['data'=>$data]);
    // }

    // public function index(){
    //     $data = [
    //         ['name'=>'山田たろう', 'mail'=>'taro@yamada'],
    //         ['name'=>'田中はなこ', 'mail'=>'hanako@flower'],
    //         ['name'=>'鈴木さちこ', 'mail'=>'sachico@happy'],
    //     ];
    //     return view('hello.index', ['data'=>$data]);
    // }

    // public function index(){
    //     return view('hello.index', ['message'=>'hello!']);
    // }

    // public function index(Request $request){
    //         return view('hello.index', ['data'=>$request->data]);
    // }

    //     public function index(Request $request){
    //             return view('hello.index');
    //     }

    // public function index(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'id' => 'required',
    //         'pass' => 'required',
    //     ]);
    //     if ($validator->fails()){
    //         $msg = 'クエリーに問題があります。';
    //     }else{
    //         $msg='ID/PASSを受け付けました。フォームを入力下さい。';
    //     }
    //     return view('hello.index', ['msg'=>$msg, ]);
    // }

    // public function index(Request $request){
    //     return view('hello.index', ['msg'=>'フォームを入力ください。']);
    // }

    // public function post(Request $request){
    //     $validate_rule = [
    //         //ルール設定
    //         'name' => 'required',
    //         'mail' => 'email',
    //         //0~150の間で入力できる
    //         'age' => 'numeric|between:0,150',
    //     ];
    //     //実際のバリデーション処理
    //     $this->validate($request, $validate_rule);
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function post(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    
    //         'mail' => 'email',
    //         'age' => 'numeric|between:0,150',
    //     ]);
    //     if ($validator->fails()){
    //         return redirect('/hello')
    //         ->withErrors($validator)
    //         ->withInput();
    //     }
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function post(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'mail' => 'email',
    //         'age' => 'numeric|between:0,150',
    //     ]);
    //     if ($validator->fails()){
    //         return redirect('/hello')
    //         ->withErrors($validator)
    //         ->withInput();
    //     }
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function post(Request $request){
    //     $rules = [
    //         'name' => 'required',
    //         'mail' => 'email',
    //         'age' => 'numeric|between:0,150',
    //     ];
    //     $messages = [
    //         'name.required' => '名前を必ず入力してください。',
    //         'mail.email' => 'メールアドレスが必要です。',
    //         'age.numeric' => '年齢を整数で記入下さい。',
    //         'age.between' => '年齢は0~150の間で入力ください。',
    //     ];
    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     if ($validator->fails()){
    //         return redirect('/hello')
    //         ->withErrors($validator)
    //         ->withInput();
    //     }
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function post(Request $request){
    //     $rules = [
    //         'name' => 'required',
    //         'mail' => 'email',
    //         'age' => 'numeric|between:0,150',
    //     ];
    //     $messages = [
    //         'name.required' => '名前を必ず入力してください。',
    //         'mail.email' => 'メールアドレスが必要です。',
    //         'age.numeric' => '年齢を整数で記入下さい。',
    //         'age.min' => '年齢はゼロ歳以上で記入ください。',
    //         'age.max' => '年齢は200歳以下で記入ください。',
    //     ];
    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     $validator->sometimes('age', 'min:0', function($input){
    //         return !is_int($input->age);
    //     });
    //     $validator->sometimes('age', 'max:200', function($input){
    //         return !is_int($input->age);
    //     });
    //     if ($validator->fails()){
    //         return redirect('/hello')
    //         ->withErrors($validator)
    //         ->withInput();
    //     }
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function post(HelloRequest $request){
    //     return view('hello.index', ['msg'=>'正しく入力されました!']);
    // }

    // public function index(Request $request){
    //     if($request->hasCookie('msg')){
    //         $msg='Cookie:'.$request->cookie('msg');
    //     }else{
    //         $msg='※クッキーはありません。';
    //     }
    //     return view('hello.index', ['msg'=> $msg]);
    // }

    // public function post(Request $request){
    //     $validate_rule=[
    //         'msg'=>'required',
    //     ];
    //     $this->validate($request, $validate_rule);
    //     $msg=$request->msg;
    //     $response=response()->view('hello.index', 
    //         ['msg'=>'「'.$msg.'」をクッキーに保存しました。']);
    //     $response->cookie('msg',$msg,100);
    //     return $response;
    // }

    // public function index(Request $request){
    //     $items=DB::select('select * from people');
    //     return view('hello.index', ['items'=> $items]);
    // }

    // public function post(Request $request){
    //     $validate_rule=[
    //         'msg'=>'required',
    //     ];
    //     $this->validate($request, $validate_rule);
    //     $msg=$request->msg;
    //     $response=response()->view('hello.index', 
    //         ['msg'=>'「'.$msg.'」をクッキーに保存しました。']);
    //     $response->cookie('msg',$msg,100);
    //     return $response;
    // }

    // public function index(Request $request){
    //     if(isset($request->id)){
    //         $param=['id'=>$request->id];
    //         $items=DB::select('select * from people where id = :id', $param);
    //     }else{
    //         $items=DB::select('select * from people');
    //     }
    //     return view('hello.index', ['items'=>$items]);
    // }

    // public function index(Request $request){
    //     $items = DB::select('select * from people');
    //     return view('hello.index', ['items'=>$items]);
    // }

    // public function index(Request $request){
    //     $items = DB::table('people')->get();
    //     return view('hello.index', ['items'=>$items]);
    // }

    public function index(Request $request){
        $items = DB::table('people')->orderBy('age', 'asc')->get();
        return view('hello.index', ['items'=>$items]);
    }

    public function post(Request $request){
        $items = DB::select('select * from people');
        return view('hello.index', ['items'=>$items]);
    }

    public function add(Request $request){
        return view('hello.add');
    }

    // public function create(Request $request){
    //     //インデックスの作成
    //     $param=[
    //         'id' => 1,
    //         'name' => $request->name,
    //         'mail' => $request->mail,
    //         'age' => $request->age,
    //     ];
    //     DB::insert('insert into people(id, name, mail, age) values(:id, :name, :mail, :age)', $param);
    //     return redirect('/hello');
    // }

    public function create(Request $request){
        //インデックスの作成
        $param=[
            'name' => $request->name,
            'mail' => $request->mail,
            'age' => $request->age,
        ];
        DB::table('people')->insert($param);
        return redirect('/hello');
    }

    // public function edit(Request $request){
    //     $param=['id'=>$request->id];
    //     $item = DB::select('select * from people where id = :id', $param);
    //     return view('hello.edit',['form'=>$item[0]]);
    // }

    // public function update(Request $request){
    //     $param=[
    //         'id' => 1,
    //         'name' => $request->name,
    //         'mail' => $request->mail,
    //         'age' => $request->age,
    //     ];
    //     DB::update('update people set name = :name, mail = :mail, age = :age where id = :id', $param);
    //     return redirect('/hello');
    // }

    public function edit(Request $request){
        $param=['id'=>$request->id];
        $item = DB::table('people')
            ->where('id', $request->id)->first();
        return view('hello.edit',['form'=>$item]);
    }

    public function update(Request $request){
        $param=[
            'name' => $request->name,
            'mail' => $request->mail,
            'age' => $request->age,
        ];
        DB::table('people')
            ->where('id',$request->id)
            ->update($param);
        return redirect('/hello');
    }

    // public function del(Request $request){
    //     $param=['id'=>$request->id];
    //     $item = DB::select('select * from people where id = :id', $param);
    //     return view('hello.del',['form'=>$item[0]]);
    // }

    // public function remove(Request $request){
    //     $param=[
    //         'id' => $request->id
    //     ];
    //     DB::delete('delete from people where id = :id', $param);
    //     return redirect('/hello');
    // }

    public function del(Request $request){
        $param=['id'=>$request->id];
        $item = DB::table('people')
            ->where('id',$request->id)
            ->first();
        return view('hello.del',['form'=>$item]);
    }

    public function remove(Request $request){
        $param=[
            'id' => $request->id
        ];
        DB::table('people')
            ->where('id',$request->id)
            ->delete();
        return redirect('/hello');
    }

    // public function show(Request $request){
    //     $id = $request->id;
    //     $item = DB::table('people')->where('id',$id)->first();
    //     return view('hello.show',['item'=>$item]);
    // }

    // public function show(Request $request){
    //     $id = $request->id;
    //     $items = DB::table('people')->where('id','<=',$id)->get();
    //     return view('hello.show',['items'=>$items]);
    // }

    // public function show(Request $request){
    //     $name = $request->name;
    //     $items = DB::table('people')
    //         ->where('name','like','%'.$name.'%')
    //         ->orWhere('mail','like','%'.$name.'%')
    //         ->get();
    //     return view('hello.show',['items'=>$items]);
    // }

//     public function show(Request $request){
//         $min = $request->min;
//         $max = $request->max;
//         $items = DB::table('people')
//             ->whereRaw('age >= ? and age <= ?',[$min, $max])
//             ->get();
//         return view('hello.show',['items'=>$items]);
//     }

    public function show(Request $request){
        $page = $request->page;
        $items = DB::table('people')
            ->offset($page * 0)
            ->limit(1)
            ->get();
        return view('hello.show',['items'=>$items]);
    }
}
