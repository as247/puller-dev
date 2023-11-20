import { Connector } from 'laravel-echo/src/connector/connector';
import {PullerChannel} from "./puller-channel";
import {PusherChannel, PusherPrivateChannel} from "laravel-echo/src/channel";
export class PullerConnector extends Connector {

    puller: any;
    channels: any = {};

    channel(name: string): PullerChannel {
        if (!this.channels[name]) {
            this.channels[name] = new PullerChannel(this.puller, name, this.options);
        }

        return this.channels[name];
    }

    connect(): void {
        if(this.options.puller){
            this.puller = this.options.puller;
        }
    }

    disconnect(): void {
        //Leave all channels
        Object.keys(this.channels).forEach((name: string, index: number) => {
            this.leaveChannel(name);
        });
    }

    leave(name: string): void {
        let channels = [name, 'private-' + name];

        channels.forEach((name: string, index: number) => {
            this.leaveChannel(name);
        });
    }
    /**
     * Leave the given channel.
     */
    leaveChannel(name: string): void {
        if (this.channels[name]) {
            this.channels[name].unsubscribe();

            delete this.channels[name];
        }
    }


    privateChannel(name: string): PullerChannel {
        return this.channel('private-' + name);
    }
    socketId(): string {
        return "";
    }

    presenceChannel(channel: string): any {
        console.error('Presence channels are not supported by Puller.');

    }

}
