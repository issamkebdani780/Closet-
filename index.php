<?php
include "config.php";
session_start();

$msg = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            header("Location: home.php");
            exit();
        } else {
            $msg = "❌ كلمة المرور غير صحيحة";
        }
    } else {
        $msg = "❌ البريد الإلكتروني غير مسجل";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
body {
    font-family: 'Tahoma', sans-serif;
    background: linear-gradient(135deg, #e8d3b8, #d9c3a8);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}

.box {
    width: 380px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    animation: fadeIn 0.8s ease;
    transition: transform 0.3s ease;
    text-align: center;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.logo {
    margin-bottom: 20px;
}

.logo img {
    width: 130px;
    transition: transform 0.5s ease;
}

.logo img:hover {
    transform: scale(1.05);
}

h2 {
    color: #2e2e2e;
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 25px;
    letter-spacing: 1px;
}

input {
    width: 100%;
    padding: 12px 20px;
    margin-top: 12px;
    border-radius: 25px;
    border: 1px solid #b89454;
    font-size: 15px;
    background: rgba(255,255,255,0.7);
    color: #2e2e2e;
    transition: 0.3s;
}

input:focus {
    border-color: #916c35;
    box-shadow: 0 0 5px rgba(145,108,53,0.5);
    outline: none;
}

button {
    width: 100%;
    margin-top: 18px;
    padding: 12px;
    background: #b89454;
    border: none;
    border-radius: 25px;
    color: white;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #916c35;
    transform: scale(1.03);
}

.msg {
    color: red;
    margin-top: 12px;
    font-size: 14px;
}

a {
    color: #b89454;
    font-weight: bold;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width:480px){
    .box{
        width:90%;
        padding:25px;
    }
    h2{ font-size:22px; }
    button{ font-size:16px; }
}
</style>
</head>
<body>

<div class="box">
    <div class="logo">
        <img src="images/logo.jpg" alt="Logo">
    </div>

    <h2>تسجيل الدخول</h2>

    <form method="POST">
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit" name="login">دخول</button>
    </form>

    <div class="msg"><?php echo $msg; ?></div>

    <p style="margin-top:12px; font-size:14px;">
        ليس لديك حساب؟
        <a href="register.php">إنشاء حساب</a>
    </p>
</div>

</body>
</html>
