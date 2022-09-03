@extends('layouts.tasktop')

@section('content')
<div class="form-wrapper">
  <h1>新規会員登録画面</h1>
  @if(session('login_errors'))
        <div style="color:red">※{{session('login_errors')}}</div>
  @endif 
  <form action="/login/insert" method="post">
    @csrf
    <div class="form-item">
      <label for="user_name">ユーザ名</label>
      @if($errors->has('user_name'))
          <div>
            <div style="color:red">※{{$errors->first('user_name')}}</div>
          </div>
      @endif
      <input type="text" name="user_name" value="{{old('user_name')}}"></input>
    </div>
    <div class="form-item">
      <label for="user_password">パスワード</label>
        @if($errors->has('user_password'))
          <div>
            <div style="color:red">※{{$errors->first('user_password')}}</div>
          </div>
        @endif
      <input type="password" name="user_password" value="{{old('user_password')}}"></input>
    </div>
    <div class="form-item">
        <label for="user_email">メールアドレス</label>
          @if($errors->has('user_email'))
            <div>
              <div style="color:red">※{{$errors->first('user_email')}}</div>
            </div>
          @endif
        <input type="text" name="user_email" value="{{old('user_email')}}"></input>
    </div>
    <div class="button-panel">
      <input type="submit" class="button" value="登録"></input>
    </div>
  </form>
  <div class="form-footer">
    <p><a href="/task/">ログインに戻る</a></p>
  </div>
</div>