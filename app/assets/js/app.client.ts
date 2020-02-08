import "../css/app.client.scss";
import 'bootstrap';
import { FilechunkWidget } from "../dist/filechunk-front/core";

for (let widget of <HTMLElement[]><any>document.querySelectorAll(".filechunk-widget-table")) {
    if (!widget.hasAttribute("data-filechunk-init")) {
        widget.setAttribute("data-filechunk-init", "1");
        new FilechunkWidget(widget, (message, variable) => {return message;});
    }
}

console.log('Welcome to frontend');
