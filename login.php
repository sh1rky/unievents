<?php
session_start();

$error = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Admin credentials
    if ($email === 'admin@unievents.tn' && $password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Admin UniEvents';
        $_SESSION['admin_email'] = 'admin@unievents.tn';
        $_SESSION['admin_role'] = 'admin';
        header('Location: dashboard.php');
        exit;
    }
    // Organisateur credentials
    elseif ($email === 'organisateur@unievents.tn' && $password === 'password') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = 2;
        $_SESSION['admin_name'] = 'Organisateur Test';
        $_SESSION['admin_email'] = 'organisateur@unievents.tn';
        $_SESSION['admin_role'] = 'organisateur';
        header('Location: dashboard.php');
        exit;
    }
    else {
        $error = 'Email ou mot de passe incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - UniEvents</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a192f 0%, #112240 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #112240;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid #233554;
        }
        .logo {
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
            color: #64ffda;
        }
        h1 {
            color: #ccd6f6;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #8892b0;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            background: #0a192f;
            border: 1px solid #233554;
            border-radius: 4px;
            color: #ccd6f6;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #64ffda;
        }
        button {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid #64ffda;
            color: #64ffda;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        button:hover {
            background: rgba(100, 255, 218, 0.1);
        }
        .error {
            background: rgba(255, 70, 70, 0.1);
            border: 1px solid #ff4646;
            color: #ff4646;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        .info {
            text-align: center;
            margin-top: 20px;
            color: #8892b0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🎓 UniEvents</div>
        <h1>Administration</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <div class="info">
            <p><strong>Admin:</strong></p>
            <p>Email: admin@unievents.tn</p>
            <p>Password: admin</p>
            <p style="margin-top: 15px;"><strong>Organisateur:</strong></p>
            <p>Email: organisateur@unievents.tn</p>
            <p>Password: password</p>
        </div>
    </div>
</body>
</html>