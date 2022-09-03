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
    @if(session('sendmail_message'))
        <div class="alert alert-success">{{session('sendmail_message')}}</div>
    @endif 
    @if(session('incomplete_message'))
        <div class="alert alert-danger">{{session('incomplete_message')}}</div>
    @endif 
    @if(session('informationinsert_message'))
        <div class="alert alert-success">{{session('informationinsert_message')}}</div>
    @endif 
    @if(session('informationupdate_message'))
    <div class="alert alert-primary">{{session('informationupdate_message')}}</div>
    @endif 
    @if(session('informationdelete_message'))
        <div class="alert alert-danger">{{session('informationdelete_message')}}</div>
    @endif 
    <h5 class="card-header">インフォメーションボード</h5>
        <table class="table table-hover">
            <tbody>
                    @foreach($informations as $information)
                        <tr>
                            <td>{{ $information->information_date }}更新</td>
                            <td>
                                <a href="/information/detail/{{$information->information_id}}">{{ $information->information_name }}</a>
                            </td>
                            @if(session('admin') == 'admin')
                                <td>
                                    <div>
                                    <a class="btn btn-danger" href="/information/delete/{{$information->information_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                    <a class="btn btn-primary" href="/information/fix/{{$information->information_id}}" role="button" style="margin: 20px;">修正</a>
                                    </div>
                            
                                </td>
                            @endif
                        </tr>
                    @endforeach
            </tbody>
        </table> 
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
                        <th scope="col">タスク日付</th>
                        <th scope="col">タスク時間</th>
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
                                <td>{{ $task->task_date }}</td>
                                <td>{{ $task->task_time }}</td>
                            </tr>
                        @elseif($task->completed == "today_incomplete")
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
                <tbody class="task">
                @foreach($tasks as $task)
                    @if($task->completed == "excess_incomplete")
                        <tr class="excess">
                            <td></td>
                            <td>&#x26a0;{{ $task->task_name }}</td>
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
                    @elseif($task->completed == "today_incomplete")
                        <tr class="successd">
                            <td></td>
                            <td>{{ $task->task_name }}</td>
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
                    @elseif($task->completed == "future_incomplete")
                        <tr>
                            <td></td>
                            <td>{{ $task->task_name }}</td>
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
                            <td>{{ $task->task_name }}</td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                            <td>
                                <div>
                                <a class="btn btn-danger" href="/task/delete/{{$task->task_id}}" role="button" style="margin: 20px;" name= "delete">削除</a>
                                <a class="btn btn-primary" href="/task/fix/{{$task->task_id}}" role="button" style="margin: 20px;">修正</a>
                                <a class="btn btn-danger" href="/task/successdenger/{{$task->task_id}}" role="button" style="margin: 20px;">完了取消</a>
                            </td>
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
