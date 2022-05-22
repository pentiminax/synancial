export class Request {
    headers;

    /**
     *
     * @param url {String}
     * @param method {String}
     * @param body {?Object}
     * @param headers {RequestInit}
     * @returns {Promise<any>}
     */
    static async fetch(url, method, body = null, headers = {}) {
        this.headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };

        this.headers = {...this.headers, ...headers};

        if (body) {
            body = JSON.stringify(body);
        }

        const response = await fetch(url, {
            method: method,
            body: body,
            headers: this.headers,
        });

        return await response.json();
    }


}