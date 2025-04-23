export function closeAllDropdowns() {
    let dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove("show");
    }
}

export function myDropdown(dropdownId) {
    closeAllDropdowns();
    const currentDropdown = document.getElementById(dropdownId);
    if (!currentDropdown.classList.contains("show")) {
        currentDropdown.classList.add("show");
    }
}

export function dropdownFunction(inputId, dropdownId) {
    let input = document.getElementById(inputId);
    let filter = input.value.toUpperCase();
    let div = document.getElementById(dropdownId);
    let a = div.getElementsByTagName("a");
    
    let noResultsMessage = div.querySelector(".no-results-message");
    if (!noResultsMessage) {
        noResultsMessage = document.createElement("p");
        noResultsMessage.className = "no-results-message";
        div.appendChild(noResultsMessage);
    }
    
    let messageText = "Nekādi dati netika atgriezti";
    if (dropdownId === "dropdown1") {
        messageText = "Tāds kurss nepastāv šajā skolā";
    } else if (dropdownId === "dropdown2") {
        messageText = "Tāds pasniedzējs nepasniedz stundas šajā skolā";
    } else if (dropdownId === "dropdown3") {
        messageText = "Tāds kabinets neatrodās nevienā no skolām";
    }
    noResultsMessage.textContent = messageText;

    noResultsMessage.style.display = "none";
    
    let matchFound = false;

    for (let i = 0; i < a.length; i++) {
        let txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
            matchFound = true;
        } else {
            a[i].style.display = "none";
        }
    }
    
    if (!matchFound && filter.length > 0) {
        noResultsMessage.style.display = "block";
    }
}

export function setupDropdownCloseListener() {
    document.addEventListener("click", function(event) {
        const dropdowns = document.getElementsByClassName("dropdown");
        let clickedInsideDropdown = false;
        for (let i = 0; i < dropdowns.length; i++) {
            if (dropdowns[i].contains(event.target)) {
                clickedInsideDropdown = true;
                break;
            }
        }
        if (!clickedInsideDropdown) {
            closeAllDropdowns();
        }
    });
}