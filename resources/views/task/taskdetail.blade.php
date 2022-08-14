@extends('layouts.taskapp')

@section('content')
<br>
<h1>タスク詳細画面</h1>

  <br>
    <table class="table table-bordered">
      @foreach($tasks as $task)
      <tr>
        <td><label for="name">タスク名</label>
        </td>
        <td>
            <span>{{$task->task_name}}</span>
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク詳細</label>
        </td>
        <td>
            <span>{{$task->task_detail}}</span>
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク日付</label>
        </td>
        <td>
            <span>{{$task->task_date}}</span>
        </td>
      </tr>
      <tr>
        <td><label for="name">タスク時間</label>
        </td>
        <td>
            <span>{{$task->task_time}}</span>
        </td>
      </tr>
      @endforeach
    </table>

  <br>

@endsection
