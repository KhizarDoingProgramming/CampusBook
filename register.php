<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $department = trim($_POST['department']);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, department) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, $department])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header("Location: index.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CampusBook</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-wrapper">

<div class="auth-card">
    <div class="auth-header">
        <div style="background: var(--primary-color); color: white; width: 60px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: var(--shadow-md);">
            <i class="fa-solid fa-graduation-cap" style="font-size: 2rem;"></i>
        </div>
        <h1 style="color: var(--primary-color); font-size: 2rem; margin-bottom: 0.5rem; font-family: var(--heading-font);">Join CampusBook</h1>
        <p style="color: var(--text-muted); font-weight: 500;">Connect with your university mates.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" placeholder="John Doe" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" placeholder="name@university.edu" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Department</label>
            <input type="text" name="department" class="form-control" placeholder="e.g., Computer Science" value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%; padding: 1rem;">Create Account</button>
    </form>
    
    <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <p style="color: var(--text-muted); font-size: 0.95rem;">Already have an account? <a href="login.php" style="font-weight: 700;">Sign In</a></p>
    </div>
</div>

</body>
</html>
