/**
 * Ajax client which try to use fetch api if available, then fallback to XMLHttpRequest.
 */
declare class Client {
    post(url: string, fields: any, options?: any): Promise<any>;
    get(url: string, options: any): Promise<any>;
    request(options: any): Promise<any>;
    parseJson(response: any): any;
}
declare const client: Client;
export default client;
