/**
 * Ajax client which try to use fetch api if available, then fallback to XMLHttpRequest.
 */
class Client {

    post(url: string, fields: any, options?: any): Promise<any>{
        return this.request({
            method: 'POST',
            url: url,
            data: fields,
            headers: options.headers
        });
    }

    get(url: string, options: any): Promise<any>{
        return this.request({
            method: 'GET',
            url: url,
            data: null,
            headers: options.headers
        });
    }
    request(options: any): Promise<any>{
        return new Promise((resolve, reject) => {
            if (typeof fetch === 'function') {
                fetch(options.url, {
                    method: options.method,
                    body: options.data,
                    headers: options.headers
                }).then((response) => {
                    resolve(response);
                }).catch((error) => {
                    reject(error);
                });
            } else {
                let request = new XMLHttpRequest();
                request.open(options.method, options.url, true);
                for (let name in options.headers) {
                    request.setRequestHeader(name, options.headers[name]);
                }
                request.onload = () => {
                    if (request.status >= 200 && request.status < 400) {
                        resolve(request.responseText);
                    } else {
                        reject(request.responseText);
                    }
                };
                request.onerror = () => {
                    reject(request.responseText);
                };
                request.send(options.data);
            }
        });
    }



}
//Export instance of Client
const client = new Client();
export default client;
