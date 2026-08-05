@extends('app.index')
@section('style')
    <style>
        .main-container {
            gap: 10px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container a {
            padding: 10px;
            color: var(--white-color);
            ;
            border-radius: 8px;
            text-decoration: none;
            background: var(--primary-color);
        }
    </style>
@endsection
@section('content')
    <div class="main-container">
        <h1 class="heading">Hospital Management Documentation</h1>
        <a href="{{ route('/documentation/logs') }}">
            <div>Logs</div>
        </a>
        <a href="{{ env('APP_URL') . '/documentation/api' }}">
            <div>Api's</div>
        </a>
    </div>
@endsection
