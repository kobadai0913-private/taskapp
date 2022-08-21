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
    @if(session('completed_message'))
        <div class="alert alert-success">{{session('completed_message')}}</div>
    @endif 
    @if(session('incomplete_message'))
        <div class="alert alert-danger">{{session('incomplete_message')}}</div>
    @endif 
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
    @if(isset($admin))
        @if($admin->admin == 'admin')
            <h5 class="card-header">ユーザのタスクの一覧</h5>  
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">ユーザID</th>
                        <th scope="col">タスク名</th>
                        <th scope="col">タスク詳細</th>
                        <th scope="col">タスク日付</th>
                        <th scope="col">タスク時間</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        @if($task->completed != "complete")
                            <tr class="successd">
                                <td></td>
                                <td>{{ $task->user_id }}</a></td>
                                <td>{{ $task->task_name }}</a></td>
                                <td>{{ $task->task_detail }}</td>
                                <td>{{ $task->task_date }}</td>
                                <td>{{ $task->task_time }}</td>
                            </tr>
                        @else
                            <tr>
                                <td></td>
                                <td>{{ $task->user_id }}</a></td>
                                <td>{{ $task->task_name }}</a></td>
                                <td>{{ $task->task_detail }}</td>
                                <td>{{ $task->task_date }}</td>
                                <td>{{ $task->task_time }}</td>
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
                    <th scope="col">タスク詳細</th>
                    <th scope="col">タスク日付</th>
                    <th scope="col">タスク時間</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    @if($task->completed != "complete")
                        <tr class="successd">
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                            <td>
                                <div>
                                <a class="btn btn-danger" href="/task/delete/{{$task->task_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                <a class="btn btn-primary" href="/task/fix/{{$task->task_id}}" role="button" style="margin: 20px;">修正</a>
                                <a class="btn btn-success" href="/task/success/{{$task->task_id}}" role="button" style="margin: 20px;">完了</a>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                            <td>
                                <div>
                                <a class="btn btn-danger" href="/task/delete/{{$task->task_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                <a class="btn btn-primary" href="/task/fix/{{$task->task_id}}" role="button" style="margin: 20px;">修正</a>
                                <a class="btn btn-success disabled" href="/task/success/{{$task->task_id}}" role="button" style="margin: 20px;">完了済</a>
                                <a class="btn btn-danger" href="/task/successdenger/{{$task->task_id}}" role="button" style="margin: 20px;">完了取消</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
    @else
        <h5 class="card-header">タスク一覧</h5>  
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">タスク名</th>
                    <th scope="col">タスク詳細</th>
                    <th scope="col">タスク日付</th>
                    <th scope="col">タスク時間</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                    @if($task->completed != "complete")
                        <tr class="successd">
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                            <td>
                                <div>
                                <a class="btn btn-danger" href="/task/delete/{{$task->task_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                <a class="btn btn-primary" href="/task/fix/{{$task->task_id}}" role="button" style="margin: 20px;">修正</a>
                                <a class="btn btn-success" href="/task/success/{{$task->task_id}}" role="button" style="margin: 20px;">完了</a>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td></td>
                            <td><a href="/task/detail/{{$task->task_id}}">{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                            <td>
                                <div>
                                <a class="btn btn-danger" href="/task/delete/{{$task->task_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                <a class="btn btn-primary" href="/task/fix/{{$task->task_id}}" role="button" style="margin: 20px;">修正</a>
                                <a class="btn btn-success disabled" href="/task/success/{{$task->task_id}}" role="button" style="margin: 20px;">完了済</a>
                                <a class="btn btn-danger" href="/task/successdenger/{{$task->task_id}}" role="button" style="margin: 20px;">完了取消</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
    @endif
    <a class="btn btn-primary" href="/task/add" role="button" style="margin: 20px;">タスクを追加する</a>
    <a class="btn btn-primary disabled" href="/task/csv/{{$user_id}}" style="margin: 20px;">CSV出力</a>
    <a class="btn btn-primary disabled" href="/task/ocr" style="margin: 20px;" disabled>OCRで遊ぶ</a>
    <a class="btn btn-primary" href="/task/zipcode" style="margin: 20px;">住所検索</a>
@endsection
