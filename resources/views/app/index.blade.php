<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital Management</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #04AA6D;
            --secondary-color: #ddd;
            --primary-bg-color: #f2f2f2;
            --secondary-bg-color: #ddd;
            --white-color: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        [type='button'],
        .button {
            border: none;
            outline: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--white-color);
            text-decoration-line: none;
            background: var(--primary-color);
        }

        ::-moz-selection {
            /* Code for Firefox */
            color: var(--white-color);
            background: var(--primary-color);
        }

        ::selection {
            color: var(--white-color);
            background: var(--primary-color);
        }

        .heading {
            font-size: 30px;
            color: var(--primary-color);
        }

        .small-heading {
            font-size: 15px;
            line-height: 23px;
            color: var(--primary-color);
        }

        .heading-in-logs {
            top: 0px;
            position: sticky;
            margin: 10px 0px;
            border-radius: 8px;
            text-align: center;
            background: #fefefe;
            position: -webkit-sticky;
            box-shadow: 0px 0px 15px lightgray;
        }

        .white-space-nowrap {
            white-space: nowrap;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .sticky {
            top: 0px;
            position: sticky;
            position: -webkit-sticky;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            min-height: 20px;
            max-height: 300px;
            overflow: auto;
            scroll-behavior: smooth;
            scrollbar-width: auto;
            scrollbar-width: thin;
            scrollbar-width: none;
            border-radius: 8px;
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .pagination {
            gap: 10px;
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center
        }

        .pagination li a {
            color: #000;
            cursor: pointer;
            border-radius: 8px;
            text-decoration-line: none;
            background: var(--secondary-bg-color);
        }

        .disabled {
            border-radius: 8px;
            cursor: not-allowed;
            background: var(--secondary-bg-color);
        }

        .active {
            cursor: pointer;
            border-radius: 8px;
            color: var(--secondary-color);
            background: var(--primary-color)
        }

        .pagination_links {
            display: flex;
            margin-top: 20px;
            align-items: center;
            justify-content: end;
            text-decoration: none;
        }

        .active,
        .disabled,
        .pagination li a {
            padding: 5px 10px;
        }
    </style>
    @yield('style')
</head>

<body class="">
    @yield('content')
    @yield('script')
</body>

</html>
