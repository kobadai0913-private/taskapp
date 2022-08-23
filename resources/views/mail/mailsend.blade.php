<!DOCTYPE html>
<html lang="ja">
    <style>
    </style>
    <body>
        <p>{{$text}}</p></br>
        <p>現時点での未完了タスクをご報告いたします。</p>
        <table>
                <thead>
                <tr>
                    <th>タスク名</th>
                    <th>タスク詳細</th>
                    <th>タスク日付</th>
                    <th>タスク時間</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->task_name }}</a></td>
                            <td>{{ $task->task_detail }}</td>
                            <td>{{ $task->task_date }}</td>
                            <td>{{ $task->task_time }}</td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </br>
        <p>下記リンクよりログイン後、タスクを完了するか日付の変更をお願いします。</p>
        <p>リンク：https://private-taskapp.herokuapp.com/task</p>
        <br>
        <p>タスク管理アプリ事務局</p>
    </body>
</html>