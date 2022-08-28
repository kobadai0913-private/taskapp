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
  
@endsection
