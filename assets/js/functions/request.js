/**
 * @param url {String}
 * @param method {String}
 * @param body {?Object}
 * @param headers {RequestInit}
 * @returns {Promise<Response>}
 */
export async function ajaxFetch(url, method, body = null, headers = {}) {
    const defaultHeaders = {
        'X-Requested-With': 'XMLHttpRequest'
    };

    headers = {...defaultHeaders, ...headers};

    if (body instanceof FormData) {
        body = Object.fromEntries(body);
    }

    if (body) {
        body = JSON.stringify(body);
    }

    console.log(body);

    return await fetch(url, {
        method: method,
        body: body,
        headers: headers,
    });

}