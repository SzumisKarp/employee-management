<?php
function sc_employee_management() {
    delete_employee_action(); // Wywołaj funkcję usuwania pracownika przed wyświetleniem listy pracowników
    return display_employee_list(); // Wyświetl listę pracowników
}
function sc_edit_employee() {
    ob_start();
    edit_employee_page();
    return ob_get_clean();
}
function sc_add_employee() {
    ob_start();
    add_employee_page();
    return ob_get_clean();
}
add_shortcode('edit_employee_page', 'sc_edit_employee');
add_shortcode('employee_management', 'sc_employee_management');
add_shortcode('add_employee_page', 'sc_add_employee');