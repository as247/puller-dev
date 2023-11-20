import {Channel} from "laravel-echo/src/channel/channel";
import {EventFormatter} from "laravel-echo/src/util";
import Puller from "../puller";


export class PullerChannel extends Channel{
    puller: Puller;
    name:string;
    options:any;
    eventFormatter: EventFormatter;
    subscription: any;
    constructor(puller: any, name: string, options: any) {
        super();
        this.name = name;
        this.puller = puller;
        this.options = options;
        this.eventFormatter = new EventFormatter(this.options.namespace);
        this.subscribe();
    }

    subscribe(): any {
        this.subscription = this.puller.channel(this.name);
    }
    unsubscribe(): void {
        this.subscription.stop();
    }

    error(callback: Function): Channel {
        this.subscription.catch(callback);
        return this;
    }

    listen(event: string, callback: Function): Channel {
        this.subscription.listen(event, callback);
        return this;
    }

    stopListening(event: string, callback?: Function): Channel {
        this.subscription.off(event);
        return this;
    }

    subscribed(callback: Function): Channel {
        this.subscription.started(callback);
        return this;
    }

}
