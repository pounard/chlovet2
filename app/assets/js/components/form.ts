const forms = <HTMLFormElement[]><any> document.querySelectorAll("form");

for (let form of forms) {
    const cards = <HTMLElement[]><any> form.querySelectorAll(`[data-open-close]`);

    for (let card of cards) {
        const headers = <HTMLElement[]><any> card.querySelectorAll(".card-header");

        // This can only work with a valid bootstrap card markup.
        for (let header of headers) {

            // Lookup for an adjacent body.
            const body = header.nextElementSibling as HTMLElement | null;
            if (!body || !body.classList.contains('card-body')) {
                console.log("Found a .card-header without adjacent .card-body");
                console.log(header);
                console.log(body);
                continue;
            }

            // So, this card header has an adjacent body, let's now lookup
            // for a checkbox inside the header.
            const checkbox = header.querySelector(`input[type="checkbox"]`) as HTMLInputElement | null;
            if (!checkbox) {
                console.log("Found a .card-header without checkbox");
                console.log(header);
                continue;
            }

            const toggle = () => {
                if (checkbox.checked) {
                    body.style.display = "block";
                } else {
                    body.style.display = "none";
                }
            }

            toggle();

            checkbox.addEventListener("change", toggle);
        }
    }
}
