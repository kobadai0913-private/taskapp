<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HelloMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * 
     * request:リクエストを管理する
     * closure:無名クラスを表すためのクラス
     */
    // public function handle($request, Closure $next)
    // {
    //     $data=[
    //         ['name'=>'taro', 'mail'=>'taro@yamada'],
    //         ['name'=>'hanako', 'mail'=>'hanako@flower'],
    //         ['name'=>'sachiko', 'mail'=>'sachiko@happy'],  
    //     ];
    //     //フォーム送信によって送られてくる値(input値)に新たな値を追加するもの
    //     //コントローラ側では$request->dataでこの値を取り出すことが出来る
    //     $request->merge(['data'=>$data]);
    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        //コントローラのアクションが実行される
        //結果を変数に代入する
        $response = $next($request);

        //レスポンスのコンテンツ取得
        $content = $response->content();

        //レスポンスに設定されているコンテンツが取得できる
        //ドメインにアクセスするためのリンクが自動生成される
        $pattern = '/<middleware>(.*)<\/middleware>/i';
        $replace = '<a href="http://$1">$1</a>';
        $content = preg_replace($pattern, $replace, $content);

        //レスポンスにコンテンツを設定して返す
        //レスポンスにコンテンツが自動設定された
        $response->setContent($content);

        return $response;
    }
}
