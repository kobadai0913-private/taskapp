@extends('layouts.tasktop')

@section('content')
<div class="form-wrapper">
  <h1>管理者としてログイン</h1>
  @if(session('login_errors'))
        <div style="color:red">※{{session('login_errors')}}</div>
  @endif 
  @if(session('insert_message'))
        <div style="color:blue">※{{session('insert_message')}}</div>
  @endif 
  <form action="/login/admin" method="post">
    @csrf
    <div class="form-item">
      <label for="email">E-Mail</label>
      @if($errors->has('email'))
          <div>
            <div style="color:red">※{{$errors->first('email')}}</div>
          </div>
      @endif
      <input type="email" name="email" value="{{old('email')}}"></input>
    </div>
    <div class="form-item">
      <label for="password">Password</label>
        @if($errors->has('password'))
          <div>
            <div style="color:red">※{{$errors->first('password')}}</div>
          </div>
        @endif
      <input type="password" name="password" value="{{old('password')}}"></input>
    </div>
    <div class="button-panel">
      <input type="submit" class="button" value="ログイン"></input>
    </div>
    <div class="form-footer">
    <p><a href="/task/">ログインに戻る</a></p>
  </div>
  </form>
</div>