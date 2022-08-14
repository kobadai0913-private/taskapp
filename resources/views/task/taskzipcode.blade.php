@extends('layouts.taskapp')

@section('content')
<br>
<h1>住所検索</h1>
<div class="block">
<form action="/task/zipcode" method="post">  
    @csrf
    郵便番号:<input type="text" name="zipcode">
    <input type="submit" class="btn btn-primary" value="住所検索" style="margin: 20px;">
</form>
<br>
@if(isset($zipcode))
<p>住所:{{$zipcode[0]}}</p>
<p>読み:{{$zipcode[1]}}</p>
@else
@endif
</div>

@endsection
