export function closeAllDropdowns() {
    let dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove("show");
    }
}

export function myDropdown(dropdownId) {
    // First, close all dropdowns
    closeAllDropdowns();

    // Then toggle the clicked dropdown
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
    for (let i = 0; i < a.length; i++) {
        let txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
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
