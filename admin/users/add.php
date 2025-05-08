<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../config/add_functions.php';

// Configuration for this entity
$entity = 'Users'; // Table name should match exactly
$entity_title = 'User';
$list_url = 'list.php';
$fields = [
    [
        'name' => 'username',
        'label' => 'Username',
        'type' => 'text',
        'required' => true,
        'help_text' => 'Must be unique'
    ],
    [
        'name' => 'email',
        'label' => 'Email',
        'type' => 'email',
        'required' => true
    ],
    [
        'name' => 'password', // This is the form field name
        'label' => 'Password',
        'type' => 'password',
        'required' => true,
        'db_field' => 'password_hash', // This maps to the database column
        'transform' => function($value) {
            return password_hash($value, PASSWORD_DEFAULT); // Hash the password before saving
        }
    ],
    [
        'name' => 'first_name',
        'label' => 'First Name',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'last_name',
        'label' => 'Last Name',
        'type' => 'text',
        'required' => true
    ],
    [
        'name' => 'is_active',
        'label' => 'Active',
        'type' => 'checkbox',
        'value' => 1,
        'help_text' => 'Uncheck to disable this account'
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
                        
                        <?php if ($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'password'): ?>
                            <input type="<?php echo $field['type']; ?>" 
                                   class="form-control" 
                                   id="<?php echo $field['name']; ?>" 
                                   name="<?php echo $field['name']; ?>"
                                   <?php if (!empty($field['required'])) echo 'required'; ?>>
                        <?php elseif ($field['type'] === 'checkbox'): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="<?php echo $field['name']; ?>" 
                                       name="<?php echo $field['name']; ?>"
                                       value="<?php echo $field['value'] ?? 1; ?>"
                                       <?php if (!empty($field['required'])) echo 'required'; ?>>
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

<?php include '../includes/footer.php'; ?>