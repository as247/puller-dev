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
        <button id="test-event">Test new OrderComplete Event</button>
        <pre>
            curl {{route('test-event')}}
        </pre>
        <div id="order-notification">
            <h1>Order Notification</h1>
            <textarea id="order-completed" style="width: 100%" rows="20"></textarea>
        </div>

        <script>
            const orderCompleted = document.getElementById('order-completed');
            document.addEventListener('DOMContentLoaded', () => {
                window.Echo.private('orders')
                    .listen('OrderCompleted', (e) => {
                        orderCompleted.value += JSON.stringify(e)+'\n';
                    });
            });

            const testEvent = document.getElementById('test-event');
            testEvent.addEventListener('click', () => {
                axios.post('/test-event', {
                    name: 'Test Event',
                    price: 100
                });
            });
        </script>

    </body>

</html>
