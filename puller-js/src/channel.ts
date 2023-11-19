import client from "./client";

export default class Channel {
    private _defaultOptions: any = {
        url:'/puller/messages',
        userAuthentication: {
            endpoint: '/broadcasting/user-auth',
            headers: {},
        },
    };

    name: string;
    token: any;
    options: any;
    events: any;
    started: boolean = false;


    /**
     * Create a new class instance.
     */
    constructor(name: string, options?: any) {
        this.name = name;
        //merge with default options
        this.options = Object.assign(this._defaultOptions, options);
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
    start() {
        if (!this.started) {
            this.started = true;
            if(this.isPrivate()){
                this.auth().then((response) => {
                    this.loop();
                });
            }else {
                this.loop();
            }
        }
    }
    auth(){
        //get token from server and return promise
        return new Promise((resolve, reject) => {
            client.post(this.options.userAuthentication.endpoint, {
                channel: this.name,
            }).then((response) => {
                if (response.data) {
                    this.token = response.data.token;
                    resolve(response.data);
                }
            }).catch((error) => {
                reject(error);
            })
        });
    }
    isPrivate(){
        //check if channel is private by checking prefix 'private-'
        return this.name.indexOf('private-') === 0;

    }
    loop(){
        client.post(this.options.url, {
            channel: this.name,
            token:this.token,
        }).then((response) => {
            if (response.data) {
                console.log(response.data);
            }
            //this.loop();
        }  ).catch((error) => {
            //this.loop();
        })
    }
}
