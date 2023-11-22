import Puller from 'puller-js';
import Echo from 'laravel-echo';
window.Echo = new Echo({
    broadcaster: Puller.echoConnect,
});
window.Echo.private('orders').listen('OrderCompleted', (e) => {
    console.log(e);
});
