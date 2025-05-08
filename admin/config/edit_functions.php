<?php
// config/edit_functions.php

function buildEditFormFields($fields, $current_data) {
    $html = '';
    
    // Check if $fields is an array and not empty
    if (!is_array($fields) || empty($fields)) {
        return '<div class="alert alert-danger">No form fields defined</div>';
    }
    
    foreach ($fields as $field) {
        // Skip if required keys are missing
        if (!isset($field['name']) || !isset($field['label']) || !isset($field['type'])) {
            continue;
        }
        
        $required = isset($field['required']) && $field['required'] ? 'required' : '';
        $value = $current_data[$field['name']] ?? '';
        
        $html .= '<div class="mb-3">';
        $html .= '<label for="' . htmlspecialchars($field['name']) . '" class="form-label">' . htmlspecialchars($field['label']);
        if ($required) $html .= ' <span class="text-danger">*</span>';
        $html .= '</label>';
        
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'password':
            case 'number':
                $html .= '<input type="' . htmlspecialchars($field['type']) . '" class="form-control" id="' . htmlspecialchars($field['name']) . '" 
                          name="' . htmlspecialchars($field['name']) . '" value="' . htmlspecialchars($value) . '" ' . $required . '>';
                break;
                
            case 'textarea':
                $html .= '<textarea class="form-control" id="' . htmlspecialchars($field['name']) . '" name="' . htmlspecialchars($field['name']) . '" 
                          rows="3" ' . $required . '>' . htmlspecialchars($value) . '</textarea>';
                break;
                
            case 'select':
                if (!isset($field['options']) || !is_array($field['options'])) {
                    break;
                }
                $html .= '<select class="form-select" id="' . htmlspecialchars($field['name']) . '" name="' . htmlspecialchars($field['name']) . '" ' . $required . '>';
                foreach ($field['options'] as $option) {
                    if (!isset($option['value']) || !isset($option['label'])) {
                        continue;
                    }
                    $selected = ($value == $option['value']) ? 'selected' : '';
                    $html .= '<option value="' . htmlspecialchars($option['value']) . '" ' . $selected . '>' . htmlspecialchars($option['label']) . '</option>';
                }
                $html .= '</select>';
                break;
                
            case 'checkbox':
                $checked = ($value) ? 'checked' : '';
                $html .= '<div class="form-check">
                            <input class="form-check-input" type="checkbox" id="' . htmlspecialchars($field['name']) . '" 
                                   name="' . htmlspecialchars($field['name']) . '" ' . $checked . '>
                            <label class="form-check-label" for="' . htmlspecialchars($field['name']) . '">' . htmlspecialchars($field['label']) . '</label>
                          </div>';
                break;
        }
        
        if (!empty($field['help_text'])) {
            $html .= '<div class="form-text">' . htmlspecialchars($field['help_text']) . '</div>';
        }
        
        $html .= '</div>';
    }
    return $html;
}

function validateAndUpdateRecord($conn, $table, $id_field, $id, $fields, $data) {
    $errors = [];
    $clean_data = [];
    
    // Validate each field
    foreach ($fields as $field) {
        // Skip if field is invalid
        if (!isset($field['name']) || !isset($field['type'])) {
            continue;
        }
        
        $field_name = $field['name'];
        $value = trim($data[$field_name] ?? '');
        
        // Skip password if empty (not updating)
        if ($field['type'] === 'password' && empty($value)) {
            continue;
        }
        
        // Required field validation
        if (isset($field['required']) && $field['required'] && empty($value)) {
            $errors[$field_name] = ($field['label'] ?? $field_name) . ' is required';
            continue;
        }
        
        // Field-specific validation
        switch ($field['type']) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field_name] = 'Invalid email format';
                }
                break;
                
            case 'number':
                if (!is_numeric($value)) {
                    $errors[$field_name] = 'Must be a number';
                }
                break;
        }
        
        $clean_data[$field_name] = $conn->real_escape_string($value);
    }
    
    // If no errors, update database
    if (empty($errors)) {
        if (empty($clean_data)) {
            return ['success' => true]; // Nothing to update
        }
        
        $updates = [];
        foreach ($clean_data as $key => $value) {
            $updates[] = "$key = '$value'";
        }
        
        $query = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $id_field = '" . $conn->real_escape_string($id) . "'";
        if ($conn->query($query)) {
            return ['success' => true];
        } else {
            $errors['database'] = 'Database error: ' . $conn->error;
        }
    }
    
    return ['success' => false, 'errors' => $errors];
}