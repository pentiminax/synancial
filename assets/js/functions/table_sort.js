import TableSort from "tablesort";

export function initializeCustomSort() {
    TableSort.extend('number', function (item) {
        return Number(item);
    }, function (a, b) {
        return a - b;
    });
}

/**
 * @param {String} selector
 */
export function initializeTableSort(selector) {
    const tables = document.querySelectorAll(selector);

    tables.forEach(table => {
        new TableSort(table);
    });
}