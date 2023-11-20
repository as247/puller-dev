/**
 * Ajax client which try to use fetch api if available, then fallback to XMLHttpRequest.
 */
class Client {

    post(url: string, fields: any, options?: any): Promise<any>{
        return this.request({
            method: 'POST',
            url: url,
            data: fields,
            headers: options ? options.headers: {}
        });
    }

    get(url: string, options: any): Promise<any>{
        return this.request({
            method: 'GET',
            url: url,
            data: null,
            headers: options ? options.headers: {}
        });
    }
    request(options: any): Promise<any>{
        return new Promise((resolve, reject) => {
            //Default headers content type to json
            options.headers = options.headers || {};
            options.headers['Content-Type'] = options.headers['Content-Type'] || 'application/json';
            if(options.headers['Content-Type']==='application/json' && typeof options.data !== 'string'){
                options.data = JSON.stringify(options.data);
            }
            if (typeof fetch === 'function') {
                fetch(options.url, {
                    method: options.method,
                    body: options.data,
                    headers: options.headers
                }).then((response) => {
                    if(response.status >= 200 && response.status < 400){
                        resolve(response.json());
                    }else{
                        reject(response.json());
                    }

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
                    let response = this.parseJson(request.responseText);
                    if (request.status >= 200 && request.status < 400) {
                        resolve(response);
                    } else {
                        reject(response);
                    }
                };
                request.onerror = () => {
                    let response = this.parseJson(request.responseText);
                    reject(response);
                };
                try {
                    request.send(options.data);
                } catch (e) {
                    reject(e);
                }
            }
        });


    }
    parseJson(response: any): any{
        try {
            return JSON.parse(response);
        } catch (e) {
            return response;
        }
    }



}
//Export instance of Client
const client = new Client();
export default client;
