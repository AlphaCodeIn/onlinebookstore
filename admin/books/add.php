<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/add_functions.php';

// Configuration for books
$entity = 'books';
$entity_title = 'Book';
$list_url = 'list.php';
$fields = [
    [
        'name' => 'title',
        'label' => 'Title',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'author_name',
        'label' => 'Author',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'isbn',
        'label' => 'ISBN',
        'type' => 'text',
        'required' => true,
        'help_text' => 'Unique 10 or 13 digit ISBN number'
    ],
    [
        'name' => 'description',
        'label' => 'Description',
        'type' => 'textarea',
        'help_text' => 'Brief summary of the book'
    ],
    [
        'name' => 'price',
        'label' => 'Price',
        'type' => 'number',
        'required' => true,
        'help_text' => 'In ₹ (Indian Rupees)',
        'step' => '0.01'
    ],
    [
        'name' => 'stock_quantity',
        'label' => 'Stock Quantity',
        'type' => 'number',
        'required' => true,
        'min' => 0
    ],
    [
        'name' => 'publisher_id',
        'label' => 'Publisher',
        'type' => 'select',
        'required' => true,
        'options' => [],
        'help_text' => 'Select the publisher'
    ],
    [
        'name' => 'publication_date',
        'label' => 'Publication Date',
        'type' => 'date',
        'help_text' => 'Date when the book was published'
    ],
    [
        'name' => 'language',
        'label' => 'Language',
        'type' => 'text',
        'help_text' => 'Primary language of the book (e.g., English, Hindi)'
    ],
    [
        'name' => 'pages',
        'label' => 'Pages',
        'type' => 'number',
        'min' => 1,
        'help_text' => 'Total number of pages'
    ],
    [
        'name' => 'category_id',
        'label' => 'Category',
        'type' => 'select',
        'required' => true,
        'options' => [],
        'help_text' => 'Select the book category'
    ],
    [
        'name' => 'cover_image_url',
        'label' => 'Cover Image URL',
        'type' => 'text',
        'help_text' => 'Direct URL to book cover image'
    ],
    [
        'name' => 'is_featured',
        'label' => 'Featured Book',
        'type' => 'checkbox',
        'help_text' => 'Check to feature this book on homepage'
    ],
    [
        'name' => 'is_active',
        'label' => 'Active',
        'type' => 'checkbox',
        'help_text' => 'Uncheck to hide this book from listings',
        'default' => true
    ]
];


$publishers = $conn->query("SELECT publisher_id, name FROM Publishers ORDER BY name");
foreach ($publishers as $pub) {
    $fields[6]['options'][] = [
        'value' => $pub['publisher_id'],
        'label' => $pub['name']
    ];
}


$categories = $conn->query("SELECT category_id, name FROM Categories ORDER BY name");
foreach ($categories as $cat) {
    $fields[10]['options'][] = [
        'value' => $cat['category_id'],
        'label' => $cat['name']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $_POST['price'] = (float)$_POST['price'];
    $_POST['stock_quantity'] = (int)$_POST['stock_quantity'];
    $_POST['pages'] = !empty($_POST['pages']) ? (int)$_POST['pages'] : null;
    $_POST['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $_POST['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    
   
    $result = validateAndAddRecord($conn, ucfirst($entity), $fields, $_POST);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "$entity_title added successfully!";
        header("Location: $list_url");
        exit();
    } else {
        $errors = $result['errors'];
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add New <?php echo $entity_title; ?></h1>
        <a href="<?php echo $list_url; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php echo buildFormFields($fields); ?>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save <?php echo $entity_title; ?>
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
