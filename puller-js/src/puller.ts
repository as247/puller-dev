import Channel from './channel';

/**
 * This class is the primary API for interacting with broadcasting.
 */
export default class Puller {
    private options: any;
    constructor(options) {
        this.options = options;
    }
    channel(channel: string): Channel {
        return new Channel(channel, this.options);
    }
}

/**
 * Export channel classes for TypeScript.
 */

