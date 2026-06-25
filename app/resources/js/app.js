import './bootstrap';

const escapeRegExp = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

const mediaUrlFromPath = (path) => {
    if (!path) return "";
    if (path.startsWith("http://") || path.startsWith("https://") || path.startsWith("/")) return path;
    return `/media-files/${path}`;
};

const isImagePath = (path, mimeType = "") => {
    return mimeType.startsWith("image/") || /\.(jpe?g|png|webp|avif|gif|svg)$/i.test(path || "");
};

const updateMediaField = (field) => {
    const input = field.querySelector("[data-media-input]");
    if (!input) return;

    const picker = field.querySelector("[data-media-picker]");
    const preview = field.querySelector("[data-media-preview]");
    const filePreview = field.querySelector("[data-file-preview]");
    const empty = field.querySelector("[data-media-empty]");
    const selected = picker?.selectedOptions?.[0] || null;
    const path = input.value.trim();
    const mimeType = selected?.dataset?.type || "";
    const url = selected?.dataset?.url || mediaUrlFromPath(path);
    const showImage = Boolean(path) && isImagePath(path, mimeType);

    if (preview) {
        preview.src = showImage ? url : "";
        preview.classList.toggle("hidden", !showImage);
    }

    if (filePreview) {
        filePreview.href = path ? url : "#";
        filePreview.classList.toggle("hidden", !path || showImage);
    }

    if (empty) {
        empty.classList.toggle("hidden", Boolean(path));
    }
};

const syncPickerFromInput = (field) => {
    const input = field.querySelector("[data-media-input]");
    const picker = field.querySelector("[data-media-picker]");
    if (!input || !picker) return;

    const match = Array.from(picker.options).find((option) => option.value === input.value.trim());
    picker.value = match ? match.value : "";
};

const refreshList = (list) => {
    const prefix = list.dataset.listPrefix;
    const itemsContainer = list.querySelector("[data-content-items]");
    if (!prefix || !itemsContainer) return;

    const items = Array.from(itemsContainer.children).filter((item) => item.matches("[data-content-list-item]"));
    const namePattern = new RegExp(`${escapeRegExp(prefix)}\\[\\d+\\]`, "g");

    items.forEach((item, index) => {
        const replacement = `${prefix}[${index}]`;
        const title = item.querySelector("[data-content-item-title]");

        if (title) {
            title.textContent = `عنصر ${index + 1}`;
        }

        item.querySelectorAll("[name]").forEach((field) => {
            field.name = field.name.replace(namePattern, replacement);
        });

        item.querySelectorAll("[data-list-prefix]").forEach((nestedList) => {
            nestedList.dataset.listPrefix = nestedList.dataset.listPrefix.replace(namePattern, replacement);
        });

        item.querySelectorAll("[data-content-list]").forEach(refreshList);
    });
};

const cloneListItem = (list, sourceItem = null) => {
    const template = list.querySelector("[data-content-list-template]");
    const item = sourceItem
        ? sourceItem.cloneNode(true)
        : template?.content?.firstElementChild?.cloneNode(true);

    if (!item) return null;

    item.querySelectorAll("[data-media-field]").forEach((field) => {
        syncPickerFromInput(field);
        updateMediaField(field);
    });

    return item;
};

document.addEventListener("change", (event) => {
    const picker = event.target.closest("[data-media-picker]");
    if (!picker) return;

    const field = picker.closest("[data-media-field]");
    const input = field?.querySelector("[data-media-input]");

    if (input) {
        input.value = picker.value;
    }

    updateMediaField(field);
});

document.addEventListener("input", (event) => {
    const input = event.target.closest("[data-media-input]");
    if (!input) return;

    const field = input.closest("[data-media-field]");
    syncPickerFromInput(field);
    updateMediaField(field);
});

document.addEventListener("click", (event) => {
    const addButton = event.target.closest("[data-content-add]");
    if (addButton) {
        const list = addButton.closest("[data-content-list]");
        const itemsContainer = list?.querySelector("[data-content-items]");
        const item = list ? cloneListItem(list) : null;

        if (item && itemsContainer) {
            itemsContainer.appendChild(item);
            refreshList(list);
        }
        return;
    }

    const duplicateButton = event.target.closest("[data-content-duplicate]");
    if (duplicateButton) {
        const item = duplicateButton.closest("[data-content-list-item]");
        const list = duplicateButton.closest("[data-content-list]");
        const clone = list && item ? cloneListItem(list, item) : null;

        if (clone && item) {
            item.after(clone);
            refreshList(list);
        }
        return;
    }

    const removeButton = event.target.closest("[data-content-remove]");
    if (removeButton) {
        const item = removeButton.closest("[data-content-list-item]");
        const list = removeButton.closest("[data-content-list]");
        const itemsContainer = list?.querySelector("[data-content-items]");
        const items = itemsContainer
            ? Array.from(itemsContainer.children).filter((child) => child.matches("[data-content-list-item]"))
            : [];

        if (items.length <= 1) {
            window.alert("لا يمكن حذف آخر عنصر. عطله من خيار ظاهر إذا لا تريد عرضه.");
            return;
        }

        if (item && list && window.confirm("حذف هذا العنصر من المسودة؟")) {
            item.remove();
            refreshList(list);
        }
    }
});

document.querySelectorAll("[data-media-field]").forEach((field) => {
    syncPickerFromInput(field);
    updateMediaField(field);
});

document.querySelectorAll("[data-content-list]").forEach(refreshList);
