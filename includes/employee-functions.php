<?php
function display_employee_list() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pracownicy';

    $per_page = 10; // Liczba pracowników na stronie
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    $employees = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY id ASC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ),
        ARRAY_A
    );

    $total_employees = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

    $total_pages = ceil($total_employees / $per_page);

    ob_start(); // Rozpocznij buforowanie wyjścia

    echo '<div class="wrap">';

    echo '<br><br><div class="button-wrapper"><a href="' . home_url('123-2/dodanie-pracownika/') . '" class="button button-primary">Dodaj nowego pracownika</a></div>';
    // Dodaj kod HTML do wyświetlenia tabeli z pracownikami
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Stanowisko</th><th>Data Zatrudnienia</th><th>Wynagrodzenie</th><th>Akcje</th></tr></thead>';
    echo '<tbody>';

    foreach ($employees as $employee) {
        echo '<tr>';
        echo '<td>' . esc_html($employee['id']) . '</td>';
        echo '<td>' . esc_html($employee['Imie']) . '</td>';
        echo '<td>' . esc_html($employee['Nazwisko']) . '</td>';
        echo '<td>' . esc_html($employee['Stanowisko']) . '</td>';
        echo '<td>' . esc_html($employee['Data_Zatrudnienia']) . '</td>';
        echo '<td>' . esc_html($employee['Wynagrodzenie']) . '</td>';
        echo '<td>';
        echo '<a href="' . home_url('/wordpress/edycja-pracownika/?action=edit&id=' . $employee['id']) . '">Edytuj</a> | ';
        echo '<a href="' . add_query_arg(array('action' => 'delete', 'id' => $employee['id'])) . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tego pracownika?\')">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    // Paginacja bez strzałek
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a class="page-number ' . ($i === $current_page ? 'current' : '') . '" href="' . esc_url(add_query_arg('paged', $i)) . '">' . '<b style="font-size: 20px;">'.$i.'</b>' . '</a>';
        if ($i < $total_pages) {
            echo '<span class="page-separator" style="font-size: 20px;"> | </span>';
        }
    }
    echo '</div>';
    echo '</div>'; // Zamknij wrap

    return ob_get_clean(); // Zakończ buforowanie i zwróć zawartość
}
function edit_employee_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pracownicy';

    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $employee_id = intval($_GET['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sprawdź, czy przesłano dane z formularza
            if (isset($_POST['submit_edit_employee'])) {
                // Pobierz dane z formularza
                $imie = sanitize_text_field($_POST['imie']);
                $nazwisko = sanitize_text_field($_POST['nazwisko']);
                $stanowisko = sanitize_text_field($_POST['stanowisko']);
                $data_zatrudnienia = sanitize_text_field($_POST['data_zatrudnienia']);
                $wynagrodzenie = intval($_POST['wynagrodzenie']);

                // Aktualizuj dane pracownika w bazie danych
                $wpdb->update(
                    $table_name,
                    array(
                        'Imie' => $imie,
                        'Nazwisko' => $nazwisko,
                        'Stanowisko' => $stanowisko,
                        'Data_Zatrudnienia' => $data_zatrudnienia,
                        'Wynagrodzenie' => $wynagrodzenie,
                    ),
                    array('id' => $employee_id)
                );
                
                // Wykonaj przekierowanie na stronę główną za pomocą JavaScript po zakończeniu akcji
                echo '<script>window.location.href="' . home_url('/wordpress/123-2/') . '";</script>';
                exit;
            }
        }

        // Pobierz dane pracownika do edycji
        $employee = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $employee_id), ARRAY_A);

        // Wyświetl formularz edycji pracownika
        echo '
        <div class="wrap">
            <form method="post">
            <label for="imie">Imię:</label>
                <input type="text" name="imie" value="' . esc_attr($employee['Imie']) . '" required><br>
                
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" name="nazwisko" value="' . esc_attr($employee['Nazwisko']) . '" required><br>

                <label for="stanowisko">Stanowisko:</label>
                <input type="text" name="stanowisko" value="' . esc_attr($employee['Stanowisko']) . '" required><br>

                <label for="data_zatrudnienia">Data Zatrudnienia:</label>
                <input type="date" name="data_zatrudnienia" value="' . esc_attr($employee['Data_Zatrudnienia']) . '" required><br>

                <label for="wynagrodzenie">Wynagrodzenie:</label>
                <input type="text" name="wynagrodzenie" value="' . esc_attr($employee['Wynagrodzenie']) . '" required><br>

                <input type="submit" name="submit_edit_employee" value="Edytuj pracownika">
            </form>
        </div>';
    }
}
function delete_employee_action() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pracownicy';

    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $employee_id = intval($_GET['id']);

        // Usuń pracownika z bazy danych
        $wpdb->delete($table_name, array('id' => $employee_id));
    }
}
function add_employee_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pracownicy';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_add_employee'])) {
        // Check if form is submitted and the submit button is clicked
        $imie = sanitize_text_field($_POST['imie']);
        $nazwisko = sanitize_text_field($_POST['nazwisko']);
        $stanowisko = sanitize_text_field($_POST['stanowisko']);
        $data_zatrudnienia = sanitize_text_field($_POST['data_zatrudnienia']);
        $wynagrodzenie = intval($_POST['wynagrodzenie']);

        // Insert new employee into the database
        $wpdb->insert(
            $table_name,
            array(
                'Imie' => $imie,
                'Nazwisko' => $nazwisko,
                'Stanowisko' => $stanowisko,
                'Data_Zatrudnienia' => $data_zatrudnienia,
                'Wynagrodzenie' => $wynagrodzenie,
            )
        );
            // Wykonaj przekierowanie na stronę główną za pomocą JavaScript po zakończeniu akcji
            echo '<script>window.location.href="' . home_url('/wordpress/123-2/') . '";</script>';
            exit;
    }
    // Display the form for adding a new employee
    echo '
    <div class="wrap">
        <h2>Dodaj nowego pracownika</h2>
        <form method="post">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" required><br>
            
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" required><br>

            <label for="stanowisko">Stanowisko:</label>
            <input type="text" name="stanowisko" required><br>

            <label for="data_zatrudnienia">Data Zatrudnienia:</label>
            <input type="date" name="data_zatrudnienia" required><br>

            <label for="wynagrodzenie">Wynagrodzenie:</label>
            <input type="text" name="wynagrodzenie" required><br>

            <input type="submit" name="submit_add_employee" value="Dodaj pracownika">
        </form>
    </div>';
}