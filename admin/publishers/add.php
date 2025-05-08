<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/add_functions.php';

// Configuration for this entity
$entity = 'Publishers';  // Table name should match exactly
$entity_title = 'Publisher';
$list_url = 'list.php';  // URL to redirect after success
$fields = [
    [
        'name' => 'name',
        'label' => 'Publisher Name',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'address',
        'label' => 'Address',
        'type' => 'text',
        'required' => false
    ],
    [
        'name' => 'phone',
        'label' => 'Phone',
        'type' => 'text',
        'required' => false
    ],
    [
        'name' => 'email',
        'label' => 'Email',
        'type' => 'email',
        'required' => false
    ],
    [
        'name' => 'website',
        'label' => 'Website',
        'type' => 'text',
        'required' => false
    ]
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add any additional validation here if needed
    $result = validateAndAddRecord($conn, $entity, $fields, $_POST);

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

    <!-- Error messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Add Publisher Form -->
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
                        
                        <?php if ($field['type'] === 'text' || $field['type'] === 'email'): ?>
                            <input type="<?php echo $field['type']; ?>" 
                                   class="form-control" 
                                   id="<?php echo $field['name']; ?>" 
                                   name="<?php echo $field['name']; ?>"
                                   value="<?php echo isset($_POST[$field['name']]) ? htmlspecialchars($_POST[$field['name']]) : ''; ?>"
                                   <?php if (!empty($field['required'])) echo 'required'; ?>>
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

<?php include '../includes/footer.php'; ?>
