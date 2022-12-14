<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//ルートパラメータの使用
//Route::get('/〇〇/{パラメータ}', function( $受け取る引数) (......));
//第1引数のアドレス部に、{パラメータ}を用意する
//第2引数に{パラメータ}に指定したテキスト部分をパラメータとして取り出されるようになる

// Route::get('/', function () {
//     return view('welcome');
// });

// $html = <<<EOF
// <html>
// <head>
// <title>hello</title>
// <style>
// body {font-size:16pt; color:#999; }
// h1 { font-size:100pt; text-align:right; color:#eee;
//     margin:-40px 0px -50px 0px; }
// </style>
// </head>
// <body>
//     <h1>hello</h1>
//     <p>This is simple page.</p>
//     <p>これは、サンプルで作ったページです。</p>
// </body>
// </html>
// EOF;

// Route::get('hello', function () {
//     return $html;
// });

// Route::get('hello/{msg}', function ($msg){
//     $html = <<<EOF
//     <html>
//     <head>
//     <title>hello</title>
//     <style>
//     body {font-size:16pt; color:#999; }
//     h1 { font-size:100pt; text-align:right; color:#eee;
//         margin:-40px 0px -50px 0px; }
//     </style>
//     </head>
//     <body>
//         <h1>hello</h1>
//         <p>{$msg}</p>
//         <p>これは、サンプルで作ったページです。</p>
//     </body>
//     </html>
//     EOF;

//         return $html;
// });

// Route::get('hello/{msg?}', function ($msg='no massage.'){
//     $html = <<<EOF
//     <html>
//     <head>
//     <title>hello</title>
//     <style>
//     body {font-size:16pt; color:#999; }
//     h1 { font-size:100pt; text-align:right; color:#eee;
//         margin:-40px 0px -50px 0px; }
//     </style>
//     </head>
//     <body>
//         <h1>hello</h1>
//         <p>{$msg}</p>
//         <p>これは、サンプルで作ったページです。</p>
//     </body>
//     </html>
//     EOF;

//         return $html;
// });

//@でアクションメソッドを呼び出す
// Route::get('hello', 'App\Http\Controllers\HelloController@index');

//ルートパラメータの利用
//いずれも?をつけて任意パラメータとする
// Route::get('hello/{id?}/{pass?}', 'App\Http\Controllers\HelloController@index');

//複数アクションの利用
//複数ページの対応
///hello:indexを呼ぶ
//hello/other:otherを呼ぶ
//アクションとアドレスの関係
//http://アプリケーションのアドレス/コントローラ/アクション
// Route::get('hello', 'App\Http\Controllers\HelloController@index');
// Route::get('hello/other', 'App\Http\Controllers\HelloController@other');

//シングルアクションコントローラの定義
//@パラメータをしなくても、シングルアクションコントローラを呼び出すことが出来る
// Route::get('hello', 'App\Http\Controllers\HelloController');

//リクエスト、レスポンスのアクション設定
// Route::get('hello', 'App\Http\Controllers\HelloController@index');

//phpテンプレートの利用
// Route::get('hello', function(){
//     //viewメソッドの定義
//     //view('フォルダ名.ファイル名');
//     return view('hello.index');
// });

//ルートパラメータをテンプレートに返す
// Route::get('hello/{id?}','App\Http\Controllers\HelloController@index');

//postルートの追記
// Route::get('hello','App\Http\Controllers\HelloController@index');

//use App\Http\Middleware\HelloMiddleware;

//自作middlewareの利用
// Route::get('hello','App\Http\Controllers\HelloController@index')->middleware(\App\Http\Middleware\HelloMiddleware::class);

//グローバルmiddlewareの利用
//グローバルミドルウェアになると、個々のグループウェアの呼び出しが不要になる
// Route::get('hello','App\Http\Controllers\HelloController@index')->middleware('hello');

//バリデーションを作成
Route::get('hello', 'App\Http\Controllers\HelloController@index');
Route::post('hello', 'App\Http\Controllers\HelloController@post');

//DBルートの作成
Route::get('hello/add', 'App\Http\Controllers\HelloController@add');
Route::post('hello/add', 'App\Http\Controllers\HelloController@create');

//DBルートの作成
Route::get('hello/edit', 'App\Http\Controllers\HelloController@edit');
Route::post('hello/edit', 'App\Http\Controllers\HelloController@update');

//DBルート作成
Route::get('hello/del', 'App\Http\Controllers\HelloController@del');
Route::post('hello/del', 'App\Http\Controllers\HelloController@remove');

//クエリビルダの利用
Route::get('hello/show', 'App\Http\Controllers\HelloController@show');

//personルートの利用
Route::get('person', 'App\Http\Controllers\PersonController@index');

//findルートの追加
Route::get('person/find', 'App\Http\Controllers\PersonController@find');
Route::post('person/find', 'App\Http\Controllers\PersonController@search');

//モデル保存ルートの追加
Route::get('person/add', 'App\Http\Controllers\PersonController@add');
Route::post('person/add', 'App\Http\Controllers\PersonController@create');

//モデル保存ルートの追加
Route::get('person/edit', 'App\Http\Controllers\PersonController@edit');
Route::post('person/edit', 'App\Http\Controllers\PersonController@update');

//モデル保存ルートの追加
Route::get('person/del', 'App\Http\Controllers\PersonController@delete');
Route::post('person/del', 'App\Http\Controllers\PersonController@remove');

Route::get('board', 'App\Http\Controllers\BoardController@index');

Route::get('board/add', 'App\Http\Controllers\BoardController@add');
Route::post('board/add', 'App\Http\Controllers\BoardController@create');

//タスク一覧画面
Route::get('task/app', 'App\Http\Controllers\TaskController@taskapp');

//タスク追加画面
Route::get('task/add', 'App\Http\Controllers\TaskController@taskinsert_p');
Route::post('task/add', 'App\Http\Controllers\TaskController@taskinsert');

//タスク詳細画面
Route::get('task/detail/{task_id?}', 'App\Http\Controllers\TaskController@taskdetail');

//タスク修正画面
Route::get('task/fix/{task_id?}', 'App\Http\Controllers\TaskController@taskfix');
Route::post('task/fix/{task_id?}', 'App\Http\Controllers\TaskController@taskfix_create');

//タスク削除
Route::get('task/delete/{task_id?}', 'App\Http\Controllers\TaskController@taskdelete');

//ログイン画面
Route::get('task', 'App\Http\Controllers\TaskController@tasklogin');
Route::post('task', 'App\Http\Controllers\TaskController@taskloginsucsess');

//新規会員登録画面
Route::get('login/insert', 'App\Http\Controllers\TaskController@logininsert');
Route::post('login/insert', 'App\Http\Controllers\TaskController@loginsucsess');

//csv出力
Route::get('task/csv/{user_id?}', 'App\Http\Controllers\TaskController@taskcsv');

//OCR
Route::get('task/ocr', 'App\Http\Controllers\TaskController@taskocr_p');
Route::post('task/ocr', 'App\Http\Controllers\TaskController@taskocr');

//住所検索
Route::get('task/zipcode', 'App\Http\Controllers\TaskController@taskzipcode');
Route::post('task/zipcode', 'App\Http\Controllers\TaskController@taskgetzipcode');
