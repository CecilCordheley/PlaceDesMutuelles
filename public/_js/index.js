const filtersState = {
    excludeSub: false,
    OpenSubject: false,
    CloseSubject: false
};

let selectedThemes = [];

document.addEventListener("DOMContentLoaded", function () {
    // Filtres logiques
    document.querySelectorAll("input[type=checkbox][name=filter]").forEach(cb => {
        cb.addEventListener("change", () => {
            filtersState[cb.id] = cb.checked;
            applyAllFilters();
        });
    });

    // Filtres thèmes
    document.querySelectorAll("input[type=checkbox][name=ThemeFilter]").forEach(cb => {
        cb.addEventListener("change", () => {
            selectedThemes = getSelectedThemes();
            applyAllFilters();
        });
    });

    // Initialisation
    selectedThemes = getSelectedThemes();
    applyAllFilters();
});

function getSelectedThemes() {
    return Array.from(document.querySelectorAll("input[name=ThemeFilter]:checked")).map(cb => cb.value);
}

function applyAllFilters() {
    document.querySelectorAll(".subject").forEach(el => {
        let isVisible = true;

        // Filtres logiques
        if (filtersState.excludeSub && !el.hasAttribute("orga")) {
            isVisible = false;
        }

        if (filtersState.OpenSubject && el.getAttribute("closed") !== "1") {
            isVisible = false;
        }

        if (filtersState.CloseSubject && el.getAttribute("closed") !== "0") {
            isVisible = false;
        }

        // Filtres par thème (si au moins une case est cochée)
        if (selectedThemes.length > 0) {
            const elTheme = el.getAttribute("theme");
            if (!selectedThemes.includes(elTheme)) {
                isVisible = false;
            }
        }

        el.style.display = isVisible ? "block" : "none";
    });
}
