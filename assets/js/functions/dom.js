/**
 * @param {string} selector
 * @return {HTMLElement|undefined}
 */
export function $(selector) {
    return document.querySelector(selector);
}

/**
 * @param {string} selector
 * @return {HTMLElement[]}
 */
export function $$(selector) {
    return Array.from(document.querySelectorAll(selector));
}