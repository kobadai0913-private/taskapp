@extends('layouts.taskapp')

@section('content')
<br>
<h1>タスク修正画面</h1>

  <br>
  @foreach($tasks as $task)
  <form action="/task/fix/{{$task->task_id}}" method="post">  
    @if(count($errors)>0)
      <div class="alert alert-danger">{{session('task_errors')}}</div>
    @endif
  @csrf
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
          <input type="text" name="task_name" value={{$task->task_name}}>
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
          <input type="text" name="task_detail" value={{$task->task_detail}}>
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
          <input type="date" style="width: 190px;" name="task_start_date" value={{$task->task_start_date}}>
          <input type="time" style="width: 190px;" name="task_start_time" value={{$task->task_start_time}}>
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
          <input type="date" style="width: 190px;" name="task_end_date" value={{$task->task_end_date}}>
          <input type="time" style="width: 190px;" name="task_end_time" value={{$task->task_end_time}}>
        </td>
      </tr>
      @endforeach
    </table>
  <br>
  <input type="submit" class="btn btn-primary" value="修正する" style="margin: 20px;">
  </form>
@endsection
