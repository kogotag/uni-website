let selected_table_cell;

function colorTableCell(element) {
    element.classList.add("table-active");
}

function uncolorTableCell(element) {
    element.classList.remove("table-active");
}

function selectTableCell(element) {
    if (selected_table_cell){
        selected_table_cell.classList.remove("table-success");
    }
    if (selected_table_cell === element){
        return;
    }
    selected_table_cell = element;
    selected_table_cell.classList.add("table-success");
}