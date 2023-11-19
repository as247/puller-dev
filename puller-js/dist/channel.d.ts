export default class Channel {
    private _defaultOptions;
    name: string;
    token: any;
    options: any;
    events: any;
    started: boolean;
    /**
     * Create a new class instance.
     */
    constructor(name: string, options?: any);
    /**
     * Listen for an event on the channel instance.
     */
    listen(event: string, callback: Function): Channel;
    start(): void;
    auth(): Promise<unknown>;
    isPrivate(): boolean;
    loop(): void;
}
