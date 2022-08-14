<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>タスク管理アプリ</title>
        <!-- cssをインポート -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">
        <!-- デフォルトのスタイルシート -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <!-- ブルーテーマの追加スタイルシート -->
        <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
        <!-- flatpickrスクリプト -->
        <script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
        <!-- 日本語化のための追加スクリプト -->
        <script src="https://npmcdn.com/flatpickr/dist/l10n/ja.js"></script>
    
    </head>
    <body>
        <!-- parts/header.blade.phpをインポートしている-->
        @include('parts.header')
        @yield('content')
        <script src="{{ mix('js/app.js') }}"></script>
        @section('contents')
    </body>
</html>
