<html >
<head>
    <title>Chat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    @vite('resources/js/app.js')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body>
    <div id="app">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 mt-5">
                    <div class="card">
                        <div class="card-header">Chat (các bạn có thể xem code của toàn bộ demo này ở https://github.com/as247/puller-dev)</div>
                        <div class="card-body">
                            <div id="messages" x-data="{
                                            messages:[],
                                            init: function(){
                                                Echo.private('chat')
                                                    .listen('MessageEvent', (e) => {
                                                        this.messages.push(e);
                                                    });
                                            }
                            }">
                                <ul class="list-group list-group-flush">
                                <template x-for="message in messages" :key="message">
                                    <li class="list-group-item">
                                    <div class="message" x-data="message" x-cloak>
                                        <div class="message-header">
                                            <span class="badge badge-primary" x-text="name"></span>
                                            <span class="badge badge-secondary" x-text="time"></span>
                                        </div>
                                        <div class="message-content">
                                            <span x-text="content"></span>
                                        </div>
                                    </div>
                                    </li>
                                </template>
                                </ul>
                            </div>
                            <form method="post" x-data="{
                                            name:'',
                                            message:'',
                                            sendMessage: function(){
                                                axios.post('/send-message', {
                                                    message: this.message,
                                                    name:this.name
                                                });
                                                this.message = '';
                                            }
                            }" x-on:submit.prevent="sendMessage" x-cloak>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Name</span>
                                    </div>
                                    <input x-model="name" type="text" class="form-control" placeholder="Name">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Message</span>
                                    </div>
                                    <input x-model="message" type="text" class="form-control" placeholder="Message">
                                </div>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
