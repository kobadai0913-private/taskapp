@extends('layouts.taskapp')

@section('content')
<br>
<h1>インフォメーション詳細画面</h1>

  <br>
    <table class="table table-bordered">
      @foreach($informations as $information)
      <tr>
        <td><label for="name">インフォメーション日付</label>
        </td>
        <td>
            <span>{{$information->information_date}}</span>
        </td>
      </tr>
      <tr>
        <td><label for="name">インフォメーション名</label>
        </td>
        <td>
            <span>{{$information->information_name}}</span>
        </td>
      </tr>
      <tr>
        <td><label for="name">インフォメーション詳細</label>
        </td>
        <td>
            <span>{{$information->information_detail}}</span>
        </td>
      </tr>
      @endforeach
    </table>

  <br>

@endsection
