import Channel from './channel';
/**
 * This class is the primary API for interacting with broadcasting.
 */
export default class Puller {
    private options;
    constructor(options: any);
    channel(channel: string): Channel;
}
/**
 * Export channel classes for TypeScript.
 */
