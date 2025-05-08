<?php
function buildTableHeaders($columns) {
    $html = '';
    foreach ($columns as $column) {
        $html .= "<th>{$column['label']}</th>";
    }
    $html .= "<th>Actions</th>";
    return $html;
}

function buildTableRow($item, $columns, $entity, $id_field = 'id') {
    $html = '';
    foreach ($columns as $column) {
        $value = $item[$column['field']] ?? '';
        
        if (isset($column['format'])) {
            switch ($column['format']) {
                case 'price':
                    $value = '₹' . number_format($value, 2);
                    break;
                case 'status':
                    $active = $item['is_active'] ?? true;
                    $value = '<span class="badge bg-' . ($active ? 'success' : 'secondary') . '">' . 
                             ($active ? 'Active' : 'Inactive') . '</span>';
                    break;
                case 'image':
                    $value = !empty($value) 
                        ? '<img src="' . htmlspecialchars($value) . '" alt="Thumbnail" style="width:50px;height:auto;">'
                        : '<div class="bg-light d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                              <i class="fas fa-image text-muted"></i>
                           </div>';
                    break;
                case 'date':
                    $value = date('M j, Y', strtotime($value));
                    break;
            }
        }
        
        $html .= "<td>{$value}</td>";
    }
    
    // Action buttonsaaa
    $item_id = $item[$id_field] ?? '';
    $html .= '<td>
    <div class="d-flex gap-2">';
    
// Check if entity is not 'category' before showing the view button
if ($entity !== 'categories') {
    $html .= '<a href="view.php?id=' . $item_id . '" class="btn btn-sm btn-outline-primary" title="View">
        <i class="fas fa-eye"></i>
    </a>';
}

$html .= '<a href="edit.php?id=' . $item_id . '" class="btn btn-sm btn-outline-secondary" title="Edit">
    <i class="fas fa-edit"></i>
</a>';

if ($entity !== 'payments' && $entity !== 'orders') {
    $html .= '<form method="post" action="delete.php" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this record?\');">
        <input type="hidden" name="id" value="' . $item_id . '">
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>';
}

$html .= '</div></td>';


    
    return $html;
}

function buildPagination($current_page, $total_pages, $query_params) {
    $html = '<nav class="mt-4"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $query_params['page'] = $current_page - 1;
        $html .= '<li class="page-item">
            <a class="page-link" href="?' . http_build_query($query_params) . '">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        $query_params['page'] = $i;
        $active = $i === $current_page ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">
            <a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a>
        </li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $query_params['page'] = $current_page + 1;
        $html .= '<li class="page-item">
            <a class="page-link" href="?' . http_build_query($query_params) . '">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
?>
