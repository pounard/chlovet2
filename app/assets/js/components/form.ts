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

    /*
    const triggered = <HTMLElement[]><any> form.querySelectorAll("[data-show-if]");

    for (let element of triggered) {
        let found = false;

        for (let condition of parseConditions(form, element)) {
            found = true;
            condition.trigger.addEventListener('change', () => toggle(condition));
            condition.trigger.addEventListener('mouseup', () => toggle(condition));
            toggle(condition);
        }

        if (!found) {
            show(element);
        }
    }
     */
}

/*
interface Condition {
    trigger: HTMLElement;
    target: HTMLElement;
    value?: string;
}

function findTrigger(context: Element, id: string): HTMLElement | null {
    return context.querySelector(`[data-trigger="${id}"]`) as HTMLElement | null;
}

function parseConditions(form: HTMLFormElement, target: HTMLElement): Condition[] {
    const conditions: Condition[] = [];
    const conditionString = target.getAttribute("data-show-if");

    if (!conditionString) {
        return conditions;
    }

    conditionString.split(';').forEach(condition => {
        let triggerId = '';
        let value = undefined;

        const pos = condition.indexOf('=');
        if (pos) {
            triggerId = condition.substring(0, pos);
            value = condition.substring(pos);
        } else {
            triggerId = condition;
        }

        if (!triggerId) {
            console.log(`Could not find a trigger id for condition ${condition}`);
            return;
        }

        const trigger = findTrigger(form, triggerId);
        if (trigger) {
            conditions.push({ trigger: trigger, target: target, value: value });
        } else {
            console.log(`Could not find triggering element for condition ${condition}`);
            console.log(trigger);
        }
    });

    return conditions;
}

function hide(element: HTMLElement): void {
    if (element.parentElement?.classList.contains('form-group')) {
        element.parentElement.style.display = "none";
    } else {
        element.style.display = "none";
    }
}

function show(element: HTMLElement): void {
    if (element.parentElement?.classList.contains('form-group')) {
        element.parentElement.style.display = "block";
    } else {
        element.style.display = "block";
    }
}

/*
function toggle(condition: Condition): void {
    if (condition.value) {
        if (condition.value === condition.trigger.value) {
            show(condition.target);
        } else {
            hide(condition.target);
        }
    } else if (condition.trigger.checked || condition.trigger.value) {
        show(condition.target);
    } else {
        hide(condition.target);
    }
}
 */
