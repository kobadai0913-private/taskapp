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

//タスク一覧画面
Route::get('task/app', 'App\Http\Controllers\TaskController@taskapp_list');

//タスク追加画面
Route::get('task/add', 'App\Http\Controllers\TaskController@task_insert');
Route::post('task/add', 'App\Http\Controllers\TaskController@task_insert_registration');

//タスク詳細画面
Route::get('task/detail/{task_id?}', 'App\Http\Controllers\TaskController@task_detail');

//タスク修正画面
Route::get('task/fix/{task_id?}', 'App\Http\Controllers\TaskController@task_fix');
Route::post('task/fix/{task_id?}', 'App\Http\Controllers\TaskController@task_fix_registration');

//タスク削除
Route::get('task/delete/{task_id?}', 'App\Http\Controllers\TaskController@task_delete');

//ログイン画面
Route::get('task', 'App\Http\Controllers\TaskController@task_login');
Route::post('task', 'App\Http\Controllers\TaskController@task_login_registration');

//新規会員登録画面
Route::get('login/insert', 'App\Http\Controllers\TaskController@new_member');
Route::post('login/insert', 'App\Http\Controllers\TaskController@new_member_registration');

//csv出力
Route::get('task/csv', 'App\Http\Controllers\TaskController@task_csv');

//OCR
Route::get('task/ocr', 'App\Http\Controllers\TaskController@taskocr_p');
Route::post('task/ocr', 'App\Http\Controllers\TaskController@taskocr');

//住所検索
Route::get('task/zipcode', 'App\Http\Controllers\TaskController@taskzipcode');
Route::post('task/zipcode', 'App\Http\Controllers\TaskController@taskgetzipcode');

//管理者ログイン
Route::get('login/admin', 'App\Http\Controllers\AppUserController@login_admin');
Route::post('login/admin', 'App\Http\Controllers\AppUserController@login_admin_registration');

//管理者権限ログイン
Route::get('login/admin/user/{user_id?}', 'App\Http\Controllers\AppUserController@user_login');

//ユーザ一覧画面
Route::get('administrator', 'App\Http\Controllers\AppUserController@user_admin_list');

//ユーザ削除
Route::get('user/delete/{user_id?}', 'App\Http\Controllers\AppUserController@user_delete');

//ユーザ修正画面
Route::get('user/fix/{user_id?}', 'App\Http\Controllers\AppUserController@user_fix');
Route::post('user/fix/{user_id?}', 'App\Http\Controllers\AppUserController@user_fix_registration');

//タスク完了更新
Route::get('task/success/{task_id?}', 'App\Http\Controllers\TaskController@task_success_update');

//タスク完了取消
Route::get('task/successdenger/{task_id?}', 'App\Http\Controllers\TaskController@task_success_denger');

//メール送信テスト
Route::get('mail/{user_id?}', 'App\Http\Controllers\MailSendController@postPurchaseComplete');

//インフォメーション追加画面
Route::get('information/add', 'App\Http\Controllers\InformationController@information_insert');
Route::post('information/add', 'App\Http\Controllers\InformationController@information_insert_registration');

//インフォメーション修正画面
Route::get('information/fix/{information_id?}', 'App\Http\Controllers\InformationController@information_fix');
Route::post('information/fix/{information_id?}', 'App\Http\Controllers\InformationController@information_fix_registration');

//インフォメーション削除
Route::get('information/delete/{information_id?}', 'App\Http\Controllers\InformationController@information_delete');

//インフォメーション詳細
Route::get('information/detail/{information_id?}', 'App\Http\Controllers\InformationController@information_detail');

