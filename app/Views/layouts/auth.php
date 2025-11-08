<?php
// We no longer need 'use' statements here, we will call helpers with full paths.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($title ?? 'Authentication') ?> | Student Management System</title>
    
    <!-- Bootstrap 5 CSS (FIXED integrity attribute) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Inline styles for centering the layout -->
    <style>
        body.auth-body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
    </style>
</head>
<body class="auth-body">

    <div class="auth-container">
        
        <div class="auth-header">
            <h1><?= htmlspecialchars($title ?? 'Welcome') ?></h1>
            <?php if (isset($title) && $title === 'Login'): ?>
                <p>Sign in to your account</p>
            <?php else: ?>
                 <p>Create your new account</p>
            <?php endif; ?>
        </div>
        
        <!-- Flash Messages (Uses full namespace path) -->
        <?php \App\Helpers\displayFlash(); ?>

        <!-- Dynamic Page Content Injection -->
        <?php echo $content; ?>
        
    </div>

    <!-- Bootstrap 5 JS Bundle (FIXED integrity attribute) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- Your custom JS file -->
    <script src="/assets/js/app.js"></script>
</body>
</html>

