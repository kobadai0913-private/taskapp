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
        <td><label for="name">タスク日付</label>
        </td>
        <td>
          @if($errors->has('task_date'))
          <div>
            <div style="color:red">※{{$errors->first('task_date')}}</div>
          </div>
          @endif
          <input type="text" name="task_date" value={{$task->task_date}}>
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク時間</label>
        </td>
        <td>
          @if($errors->has('task_time'))
          <div>
            <div style="color:red">※{{$errors->first('task_time')}}</div>
          </div>
          @endif
          <input type="text" name="task_time" value={{$task->task_time}}>
        </td>
      </tr>
      @endforeach
    </table>
  <br>
  <input type="submit" class="btn btn-primary" value="修正する" style="margin: 20px;">
  </form>
@endsection
