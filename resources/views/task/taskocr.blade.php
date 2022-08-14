@extends('layouts.taskapp')

@section('content')
<br>
<h1>OCRで遊ぼう</h1>
<div class="block">
<form action="/task/ocr" method="post">  
    @csrf
    <input type="file" name="file_name">
    <input type="submit" class="btn btn-primary" value="ocr出力" style="margin: 20px;">
</form>
<br>
@if(isset($ocr))
    @foreach($ocr as $data)
    <p>{{$data}}</p>
    @endforeach
@else
@endif
</div>

@endsection
