<?php
function create_employee_database() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pracownicy';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        Imie TEXT,
        Nazwisko TEXT,
        Stanowisko TEXT,
        Data_Zatrudnienia DATE,
        Wynagrodzenie INT,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_employee_database');