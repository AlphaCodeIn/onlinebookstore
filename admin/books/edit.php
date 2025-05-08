<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/add_functions.php';

// Configuration for this entity
$entity = 'Books'; // Table name should match exactly
$entity_title = 'Book';
$list_url = 'list.php';
$fields = [
    [
        'name' => 'isbn',
        'label' => 'ISBN',
        'type' => 'text',
        'required' => true,
        'help_text' => 'Must be unique'
    ],
    [
        'name' => 'title',
        'label' => 'Title',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'author_name',
        'label' => 'Author Name',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'description',
        'label' => 'Description',
        'type' => 'textarea',
        'required' => false
    ],
    [
        'name' => 'publisher_id',
        'label' => 'Publisher',
        'type' => 'select',
        'options' => [], // Will be populated from the database
        'required' => true
    ],
    [
        'name' => 'category_id',
        'label' => 'Category',
        'type' => 'select',
        'options' => [], // Will be populated from the database
        'required' => true
    ],
    [
        'name' => 'publication_date',
        'label' => 'Publication Date',
        'type' => 'date',
        'required' => false
    ],
    [
        'name' => 'language',
        'label' => 'Language',
        'type' => 'text',
        'required' => false
    ],
    [
        'name' => 'pages',
        'label' => 'Pages',
        'type' => 'number',
        'required' => true
    ],
    [
        'name' => 'price',
        'label' => 'Price',
        'type' => 'number',
        'required' => true,
        'step' => '0.01'
    ],
    [
        'name' => 'stock_quantity',
        'label' => 'Stock Quantity',
        'type' => 'number',
        'required' => true
    ],
    [
        'name' => 'cover_image_url',
        'label' => 'Cover Image URL',
        'type' => 'text',
        'required' => false
    ],
    [
        'name' => 'is_featured',
        'label' => 'Featured',
        'type' => 'checkbox',
        'value' => 1,
        'required' => false
    ],
    [
        'name' => 'is_active',
        'label' => 'Active',
        'type' => 'checkbox',
        'value' => 1,
        'required' => false
    ]
];

// Fetching book data if the ID is passed for editing
if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];
    $query = "SELECT * FROM Books WHERE book_id = $book_id LIMIT 1";
    $book = $conn->query($query)->fetch_assoc();

    if (!$book) {
        // Redirect to list page if book not found
        header('Location: list.php');
        exit;
    }

    // Populate publisher options
    $publishers_query = "SELECT publisher_id, name FROM Publishers ORDER BY name";
    $publishers = $conn->query($publishers_query);
    $publisher_options = [];
    while ($publisher = $publishers->fetch_assoc()) {
        $publisher_options[] = $publisher;
    }

    // Populate category options
    $categories_query = "SELECT category_id, name FROM Categories ORDER BY name";
    $categories = $conn->query($categories_query);
    $category_options = [];
    while ($category = $categories->fetch_assoc()) {
        $category_options[] = $category;
    }

} else {
    header('Location: list.php');
    exit;
}

// Handle form submission for editing book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = validateAndAddRecord($conn, $entity, $fields, $_POST, $book_id);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "$entity_title updated successfully!";
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
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Edit <?php echo $entity_title; ?></h1>
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
                <form method="POST">
                    <?php foreach ($fields as $field): ?>
                        <div class="mb-3">
                            <label for="<?php echo $field['name']; ?>" class="form-label">
                                <?php echo $field['label']; ?>
                                <?php if (!empty($field['required'])): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'number' || $field['type'] === 'password'): ?>
                                <input type="<?php echo $field['type']; ?>" 
                                       class="form-control" 
                                       id="<?php echo $field['name']; ?>" 
                                       name="<?php echo $field['name']; ?>"
                                       value="<?php echo htmlspecialchars($book[$field['name']]); ?>"
                                       <?php if (!empty($field['required'])) echo 'required'; ?>
                                       <?php if (!empty($field['step'])) echo 'step="' . $field['step'] . '"'; ?>>
                            <?php elseif ($field['type'] === 'textarea'): ?>
                                <textarea class="form-control" 
                                          id="<?php echo $field['name']; ?>" 
                                          name="<?php echo $field['name']; ?>"><?php echo htmlspecialchars($book[$field['name']]); ?></textarea>
                            <?php elseif ($field['type'] === 'select'): ?>
                                <select class="form-select" 
                                        id="<?php echo $field['name']; ?>" 
                                        name="<?php echo $field['name']; ?>" 
                                        required>
                                    <option value="">Select <?php echo $field['label']; ?></option>
                                    <?php if ($field['name'] == 'publisher_id'): ?>
                                        <?php foreach ($publisher_options as $publisher): ?>
                                            <option value="<?php echo $publisher['publisher_id']; ?>" 
                                                    <?php echo $book['publisher_id'] == $publisher['publisher_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($publisher['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php elseif ($field['name'] == 'category_id'): ?>
                                        <?php foreach ($category_options as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>" 
                                                    <?php echo $book['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            <?php elseif ($field['type'] === 'checkbox'): ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="<?php echo $field['name']; ?>" 
                                           name="<?php echo $field['name']; ?>"
                                           value="<?php echo $field['value'] ?? 1; ?>"
                                           <?php echo $book[$field['name']] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?php echo $field['name']; ?>">
                                        <?php echo $field['label']; ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                            

                            <?php if (!empty($field['help_text'])): ?>
                                <div class="form-text"><?php echo $field['help_text']; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save <?php echo $entity_title; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
