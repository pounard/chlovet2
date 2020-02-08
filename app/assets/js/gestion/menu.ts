// import "react-sortable-tree/style.css";
import { createTreeWidget } from "../components/menu";

const elements = document.querySelectorAll('[data-arborescence]');

if (elements.length) {
    for (let i = 0; i < elements.length; i++) {
        const element = elements[i];

        createTreeWidget(
            element,
            element.getAttribute("get-url") || '',
            element.getAttribute("post-url") || ''
        );
    }
}
