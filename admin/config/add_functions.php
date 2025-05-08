<?php
// config/add_functions.php

function buildFormFields($fields) {
    $html = '';
    foreach ($fields as $field) {
        $required = isset($field['required']) && $field['required'] ? 'required' : '';
        $value = $_POST[$field['name']] ?? '';
        
        $html .= '<div class="mb-3">';
        $html .= '<label for="' . $field['name'] . '" class="form-label">' . $field['label'];
        if ($required) $html .= ' <span class="text-danger">*</span>';
        $html .= '</label>';
        
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'password':
            case 'number':
                $html .= '<input type="' . $field['type'] . '" class="form-control" id="' . $field['name'] . '" 
                          name="' . $field['name'] . '" value="' . htmlspecialchars($value) . '" ' . $required . '>';
                break;
                
            case 'textarea':
                $html .= '<textarea class="form-control" id="' . $field['name'] . '" name="' . $field['name'] . '" 
                          rows="3" ' . $required . '>' . htmlspecialchars($value) . '</textarea>';
                break;
                
            case 'select':
                $html .= '<select class="form-select" id="' . $field['name'] . '" name="' . $field['name'] . '" ' . $required . '>';
                foreach ($field['options'] as $option) {
                    $selected = ($value == $option['value']) ? 'selected' : '';
                    $html .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
                }
                $html .= '</select>';
                break;
                
            case 'checkbox':
                $checked = isset($_POST[$field['name']]) ? 'checked' : '';
                $html .= '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="' . $field['name'] . '" 
                                   name="' . $field['name'] . '" ' . $checked . '>
                            <label class="form-check-label" for="' . $field['name'] . '">' . $field['label'] . '</label>
                          </div>';
                break;
        }
        
        if (!empty($field['help_text'])) {
            $html .= '<div class="form-text">' . $field['help_text'] . '</div>';
        }
        
        $html .= '</div>';
    }
    return $html;
}

function validateAndAddRecord($conn, $table, $fields, $data) {
    $errors = [];
    $clean_data = [];

    foreach ($fields as $field) {
        $field_name = $field['name'];
        $value = trim($data[$field_name] ?? '');

        if ($field['type'] === 'checkbox') {
            $value = isset($data[$field_name]) ? 1 : 0;
        }

        // Required check
        if (!empty($field['required']) && $value === '') {
            $errors[] = $field['label'] . ' is required.';
            continue;
        }

        // Field-specific validation
        switch ($field['type']) {
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = $field['label'] . ' must be a valid email.';
                }
                break;
            case 'number':
                if (!empty($value) && !is_numeric($value)) {
                    $errors[] = $field['label'] . ' must be a number.';
                }
                break;
        }

        $clean_data[$field_name] = $conn->real_escape_string($value);
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    // Build the SQL query to insert data into the table
    $columns = implode(',', array_keys($clean_data));
    $values = "'" . implode("','", array_values($clean_data)) . "'";

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";

    if ($conn->query($sql)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'errors' => ['Database error: ' . $conn->error]];
    }
}


