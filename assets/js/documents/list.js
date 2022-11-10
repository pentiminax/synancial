import {Chart, registerables} from "chart.js";
import {$, $$} from "../functions/dom";
import {ajaxFetch} from "../functions/request";

document.addEventListener('DOMContentLoaded', async () => {
    const documentsList = new DocumentList();
    await documentsList.loadThumbnails();
});

class DocumentList {
    constructor() {
    }

    async loadThumbnails() {
        for (const thumbnail of $$("[class*=thumbnail]")) {
            const response = await ajaxFetch(`/thumbnail/${thumbnail.dataset.idDocument}/${thumbnail.dataset.webid}`);
            const blob = await response.blob();

            const image = document.createElement('img');
            image.classList.add('card-img-top');
            image.src = URL.createObjectURL(new Blob([blob]));

            $(`.thumbnail-${thumbnail.dataset.idThumbnail}`).replaceWith(image);
        }
    }
}