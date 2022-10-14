let selectedCell;
document.querySelectorAll("#selectableCell").forEach(cell => {
    cell.addEventListener("click", selectCell);
});

function selectCell(event) {
    if (selectedCell === event.target) {
        selectedCell.classList.remove("table-success");
        return;
    }

    if (selectedCell) {
        selectedCell.classList.remove("table-success");
    }

    event.target.classList.add("table-success");
    selectedCell = event.target;
}