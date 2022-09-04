@extends('layouts.taskapp')

@section('content')
<br>
<h1>タスク追加画面</h1>

  <br>
  <form action="/task/add" method="post">  
    @csrf
    @if(count($errors)>0)
      <div class="alert alert-danger">{{session('task_errors')}}</div>
    @endif
    <table class="table table-bordered">
      <tr>
        <td><label for="name">タスク名</label>
        </td>
        <td>
          @if($errors->has('task_name'))
          <div>
            <div style="color:red">※{{$errors->first('task_name')}}</div>
          </div>
          @endif
          <input type="text" name="task_name" placeholder="xxxxxxxx" value="{{old('task_name')}}">
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク詳細</label>
        </td>
        <td>
          @if($errors->has('task_detail'))
          <div>
            <div style="color:red">※{{$errors->first('task_detail')}}</div>
          </div>
          @endif
          <input type="text" name="task_detail" placeholder="xxxxxxxx" value="{{old('task_detail')}}">
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク開始日付</label>
        </td>
        <td>
          @if($errors->has('task_start_date'))
          <div>
            <div style="color:red">※{{$errors->first('task_start_date')}}</div>
          </div>
          @endif
          @if($errors->has('task_start_time'))
          <div>
            <div style="color:red">※{{$errors->first('task_start_time')}}</div>
          </div>
          @endif
          <input type="date" name="task_start_date" style="width: 190px;" value="{{old('task_start_date')}}">
          <input type="time" name="task_start_time" style="width: 190px;" value="{{old('task_start_time')}}">
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク終了日付</label>
        </td>
        <td>
          @if($errors->has('task_end_date'))
          <div>
            <div style="color:red">※{{$errors->first('task_end_date')}}</div>
          </div>
          @endif
          @if($errors->has('task_end_time'))
          <div>
            <div style="color:red">※{{$errors->first('task_end_time')}}</div>
          </div>
          @endif
          <input type="date" name="task_end_date" style="width: 190px;" value="{{old('task_end_date')}}">
          <input type="time" name="task_end_time" style="width: 190px;" value="{{old('task_end_time')}}">
        </td>
      </tr>
    </table>
  <br>
  <input type="submit" class="btn btn-primary" value="追加する" style="margin: 20px;">
  </form>
@endsection
