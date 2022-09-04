@extends('layouts.taskapp')

@section('content')
    <br>    
    @if(session('delete_message'))
        <div class="alert alert-danger">{{session('delete_message')}}</div>
    @endif 
    @if(session('insert_message'))
        <div class="alert alert-success">{{session('insert_message')}}</div>
    @endif 
    @if(session('update_message'))
        <div class="alert alert-primary">{{session('update_message')}}</div>
    @endif 
    @if(session('csvoutput_message'))
        <div class="alert alert-primary">{{session('csvoutput_message')}}</div>
    @endif 
    @if(session('sendmail_message'))
        <div class="alert alert-success">{{session('sendmail_message')}}</div>
    @endif 
    @if(session('informationinsert_message'))
        <div class="alert alert-success">{{session('informationinsert_message')}}</div>
    @endif 
    @if(session('informationdelete_message'))
        <div class="alert alert-danger">{{session('informationdelete_message')}}</div>
    @endif 
    <h5 class="card-header">インフォメーションボード</h5>
                    @foreach($informations as $information)
                        @if(($information->{$user_name.'_flg'})==false)
                                <dl>
                                    <dt><a class="new">NEW!</a>{{ $information->information_date }}更新</dt>
                                    <dd><a href="/information/detail/{{$information->information_id}}">{{ $information->information_name }}</a></dd>
                                </dl>
                        @else
                                <dl>
                                    <dt>{{ $information->information_date }}更新</dt>
                                    <dd><a href="/information/detail/{{$information->information_id}}">{{ $information->information_name }}</a></dd>
                                </dl>
                        @endif
                    @endforeach
    <h5 class="card-header">北海道コロナ情報　　　{{$date}}現在</h5>
    <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">感染者数</th>
                <th scope="col">死亡者数</th>
                <th scope="col">PCR検査数</th>
                <th scope="col">入院患者数</th>
                <th scope="col">退院数</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$api[0]}}</td>
                    <td>{{$api[1]}}</td>
                    <td>{{$api[2]}}</td>
                    <td>{{$api[3]}}</td>
                    <td>{{$api[4]}}</td>
                </tr>
            </tbody>
    </table> 
        @if(session('admin') == 'admin')
            <h5 class="card-header">ユーザのタスクの一覧</h5>  
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">ユーザID</th>
                        <th scope="col">タスク名</th>
                        <th scope="col">タスク詳細</th>
                        <th scope="col">タスク開始日付</th>
                        <th scope="col">タスク終了日付</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="task">
                    @foreach($tasks as $task)
                        @if($task->completed == "excess_incomplete")
                            <tr class="excess">
                                <td></td>
                                <td>{{ $task->user_id }}</a></td>
                                <td>{{ $task->task_name }}</a></td>
                                <td>{{ $task->task_detail }}</td>
                                <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                                <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                            </tr>
                        @elseif($task->completed == "today_incomplete")
                            <tr class="successd">
                                <td></td>
                                <td>{{ $task->user_id }}</a></td>
                                <td>{{ $task->task_name }}</a></td>
                                <td>{{ $task->task_detail }}</td>
                                <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                                <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                            </tr>
                        @else
                            <tr>
                            <td></td>
                                <td>{{ $task->user_id }}</a></td>
                                <td>{{ $task->task_name }}</a></td>
                                <td>{{ $task->task_detail }}</td>
                                <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                                <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
        @else
            <h5 class="card-header">タスク一覧</h5>  
            <table class="table table-hover">
                <thead>
                <tr>
                        <th scope="col">No</th>
                        <th scope="col">タスク名</th>
                        <th scope="col">タスク開始日付</th>
                        <th scope="col">タスク終了日付</th>
                </tr>
                </thead>
                <tbody class="task">
                @foreach($tasks as $task)
                    @if($task->completed == "excess_incomplete")
                        <tr class="excess">
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">&#x26a0;{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                            <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                        </tr>
                    @elseif($task->completed == "today_incomplete")
                        <tr class="successd">
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                            <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                        </tr>
                    @else
                        <tr>
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_start_date }} {{ $task->task_start_time }}</td>
                            <td>{{ $task->task_end_date }} {{ $task->task_end_time }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
    @if(session('admin') != 'admin')
        <a class="btn btn-primary" href="/task/add" role="button" style="margin: 20px;">タスクを追加する</a>
    @endif
    @if(session('admin') == 'admin')
        <a class="btn btn-primary" href="/information/add" role="button" style="margin: 20px;">インフォメーションを追加する</a>
    @endif
        <a class="btn btn-primary disabled" href="/task/csv/{{session('user_id')}}" style="margin: 20px;">CSV出力</a>
@endsection
