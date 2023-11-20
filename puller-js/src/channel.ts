import client from "./client";

export default class Channel {
    private _defaultOptions: any = {
        error_delay: 10000,
        url:'/puller/messages',
        auth: {
            endpoint: '/broadcasting/auth',
            headers: {},
            data:{},
        },
    };

    name: string;
    token: any;
    options: any;
    events: any;
    onErrors: any;
    started: boolean = false;
    stopped: boolean = false;


    /**
     * Create a new class instance.
     */
    constructor(name: string, options?: any) {
        this.name = name;
        //merge with default options
        this.options = Object.assign({},this._defaultOptions, options);
        this.events = {};
        this.onErrors = [];
    }
    catch(callback: Function): Channel {
        this.onErrors.push(callback);
        return this;
    }
    /**
     * Listen for an event on the channel instance.
     */
    listen(event: string, callback: Function): Channel {
        this.events = this.events || {};
        this.events[event] = callback;
        this.start();
        return this;
    }
    on(event: string, callback: Function): Channel {
        return this.listen(event, callback);
    }
    off(event: string): Channel {
        if(this.events[event]){
            delete this.events[event];
        }
        return this;
    }

    start() {
        if (!this.started) {
            this.stopped = false;
            if(this.isPrivate()){
                this.auth().then((response) => {
                    this.loop();
                }).catch((error) => {

                });
            }else {
                this.loop();
            }
        }
    }
    stop() {
        this.stopped = true;
        this.started = false;
    }
    auth(){
        //get token from server and return promise
        let authData = Object.assign({},this.options.auth.data, {
            channel_name: this.name,
        });
        return new Promise((resolve, reject) => {
            client.post(this.options.auth.endpoint, authData,{
                headers: this.options.auth.headers,
            }).then((response) => {
                console.log(response);
                if (response.token) {
                    this.token = response.token;
                    resolve(response);
                }
            }).catch((error) => {
                reject(error);
            })
        });
    }
    isPrivate(){
        //check if channel is private by checking prefix 'private'
        return this.name.indexOf('private-') === 0;

    }
    loop(){
        //Start looping
        this.started = true;
        if(this.stopped){
            return;
        }
        let startTimestamp = new Date().getTime();
        client.post(this.options.url, {
            channel: this.name,
            token:this.token,
        }).then((response) => {
            if (response.messages) {
                response.messages.forEach((message) => {
                    if (this.events[message[0]]) {
                        this.events[message[0]](message[1]);
                    }
                    if(this.events['*']){
                        this.events['*'](message[1], message[0]);
                    }
                });
            }
            if(response.token){
                this.token = response.token;
                this.loop();
            }else{
                this.callErrors(response);
                setTimeout(() => {
                        this.loop();
                    },
                    Math.max(0,(this.options.error_delay || 10000) - (new Date().getTime() - startTimestamp))
                );
            }
        }  ).catch((error) => {
            this.callErrors(error);
            setTimeout(() => {
                this.loop();
            }, Math.max(0,(this.options.error_delay || 10000) - (new Date().getTime() - startTimestamp)));

        })
    }
    callErrors(error: any){
        this.onErrors.forEach((callback) => {
            callback(error);
        });
    }
}
