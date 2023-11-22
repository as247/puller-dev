<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>Laravel</title>
        @vite('resources/js/app.js')
    </head>
    <body>
        <div id="app">
            <h1>Hello World</h1>
            @if(auth()->check())
            <h2>Logged in with: {{auth()->user()->name}}</h2>
            @endif
        </div>
        <div id="order-notification">
            <h1>Order Notification</h1>
            <textarea id="order-completed"></textarea>
        </div>

    </body>

</html>
