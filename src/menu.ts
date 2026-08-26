// Fetch drinks

type Drink = {
    id_drink: number;
    name: string;
    price: string | number;
    stock: number;
    id_category: number;
    category: string;
}

async function fetchDrinks() {
    const response = await fetch("api/bebidas.php");
    if (!response.ok) throw new Error("Couldn't load the menu.");

    const data: Drink[] = await response.json();
    const categoryList = document.getElementById("category-listfetchDrinks");

    if (!categoryList) return;

    const categories = new Map<string, Drink[]>();

    for (const drink of data) {
        const items = categories.get(drink.category) ?? [];
        items.push(drink);
        categories.set(drink.category, items);
    }

    const menuOptions = document.getElementById("menu-options");
    if (!menuOptions) return;

    if (categories.size === 0) {
        categoryList.textContent = "No items in this category.";
        return;
    }

    const options = new Map<string, Drink[] | null>([
        ["All", null],
        ...categories
    ]);

    for (const [optionName, items] of options) {
        const button = document.createElement("button");
        button.className = "menu-option";
        button.type = "button";
        button.role = "tab";
        button.textContent = optionName;
        button.setAttribute("aria-selected", "false");
        menuOptions.appendChild(button);

        button.addEventListener("click", () => {
            document
                .querySelectorAll<HTMLButtonElement>(".menu-option")
                .forEach((option) => {
                    option.classList.remove("active");
                    option.setAttribute("aria-selected", "false");
                });

            button.classList.add("active");
            button.setAttribute("aria-selected", "true");

            renderCategories(
                items ? new Map([[optionName, items]]) : categories,
                categoryList
            );
        });
    }

    menuOptions.querySelector<HTMLButtonElement>(".menu-option")?.click();
}

function renderCategories(
    categories: Map<string, Drink[]>,
    container: HTMLElement
) {
    container.replaceChildren();

    for (const [categoryName, items] of categories) {
        const section = document.createElement("section");
        section.className = "category";

        const title = document.createElement("h2");
        title.textContent = categoryName;

        const list = document.createElement("ul");
        list.className = "drink-list";

        for (const drink of items) {
            const item = document.createElement("li");
            item.className = "drink";

            const name = document.createElement("span");
            name.className = "name";
            name.textContent = drink.name;

            const price = document.createElement("span");
            price.className = "price";
            price.textContent = Number(drink.price).toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            });

            item.append(name, price);
            list.appendChild(item);
        }

        section.append(title, list);
        container.appendChild(section);
    }
}

fetchDrinks().catch((error: Error) => {
    const categoryList = document.getElementById("category-list");
    if (categoryList) categoryList.textContent = error.message;
});