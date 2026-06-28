<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ---------- CONNECTION BDD ---------- */
$conn = new mysqli("sql303.infinityfree.com", "if0_40611001", "rS3HAXQQqxmE", "if0_40611001_closet");
if ($conn->connect_error) { die("Erreur: " . $conn->connect_error); }

/* ---------- INFO USER ---------- */
$user_sql = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_sql->fetch_assoc();

/* ---------- COUNT ARTICLES ---------- */
$articles_sql = $conn->query("SELECT COUNT(*) AS total FROM closet_items WHERE user_id = $user_id");
$articles = $articles_sql->fetch_assoc()['total'];

/* ---------- COUNT ANALYSES ---------- */
$analysis_sql = $conn->query("SELECT COUNT(*) AS total FROM closet_analysis WHERE user_id = $user_id");
$analysis = $analysis_sql->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f2efe9;
            font-family: 'Tajawal', sans-serif;
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #D4AF37 0%, #E6C97C 50%, #F5F5DC 100%);
            padding: 30px 15px 90px;
            border-bottom-left-radius: 35px;
            border-bottom-right-radius: 35px;
            text-align: center;
            color: black;
        }

        /* PROFILE CARD (simple + beau) */
        .profile-card {
            background: white;
            width: 92%;
            margin: auto;
            margin-top: -60px;
            padding: 65px 20px 20px;
            border-radius: 25px;
            box-shadow: 0px 12px 22px rgba(0,0,0,0.10);
            text-align: center;
            position: relative;
        }

        .profile-icon-wrapper {
            width: 110px;
            height: 110px;
            background: linear-gradient(135deg, #D4AF37, #E6C97C);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 55px;
            border: 4px solid white;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
            position: absolute;
            top: -55px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* ========= SERVICE CARD ========= */
        .service-card {
            background: white;
            border-radius: 30px;
            padding: 22px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.25s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(0,0,0,0.15);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f1e3;
        }

        .icon-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .service-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .service-sub {
            font-size: 14px;
            margin-top: 4px;
            color: #D4AF37;
        }

        .text-side {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header"></div>

    <!-- PROFILE CARD -->
    <div class="profile-card">

        <div class="profile-icon-wrapper">
            <i class="bi bi-person-fill"></i>
        </div>

        <h4 class="mt-3"><?= $user['nom'] ?></h4>
        <p class="text-muted small"><?= $user['email'] ?></p>

        <div class="row mt-3 text-center g-2">

            <div class="col-4">
                <p class="text-muted small"><?= $user['gender'] ?></p>
            </div>

            <div class="col-4">
                <p class="text-muted small">المقالات: <?= $articles ?></p>
            </div>

            <div class="col-4">
                <p class="text-muted small">التحليلات: <?= $analysis ?></p>
            </div>

        </div>

    </div>


    <!-- ================= SERVICES AVEC IMAGES ================= -->
    <div class="container mt-4">

        <div class="col-12 mb-3">
            <a href="add_old.php" class="text-decoration-none text-dark">
                <div class="service-card">
                    <div class="text-side">
                        <p class="service-title">تحليل قطعة قديمة</p>
                        <p class="service-sub">مراجعة و تجديد</p>
                    </div>
                    <div class="icon-box">
                        <img src="images/1.PNG" alt="1">
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 mb-3">
            <a href="add_new.php" class="text-decoration-none text-dark">
                <div class="service-card">
                    <div class="text-side">
                        <p class="service-title">تحليل قطعة جديدة</p>
                        <p class="service-sub">عند الشراء + تنسيق</p>
                    </div>
                    <div class="icon-box">
                        <img src="images/3.PNG" alt="تحليل قطعة جديدة">
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 mb-3">
            <a href="my_closet_expert.php" class="text-decoration-none text-dark">
                <div class="service-card">
                    <div class="text-side">
                        <p class="service-title">خزانتي</p>
                        <p class="service-sub">تنظيم و إستكشاف</p>
                    </div>
                    <div class="icon-box">
                        <img src="images/5.PNG" alt="خزانتي">
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 mb-3">
            <a href="dashboard.php" class="text-decoration-none text-dark">
                <div class="service-card">
                    <div class="text-side">
                        <p class="service-title">إحصائيات</p>
                        <p class="service-sub">تحليلات و اتجاهات</p>
                    </div>
                    <div class="icon-box">
                        <img src="images/7.PNG" alt="إحصائيات">
                    </div>
                </div>
            </a>
        </div>

    </div>

</body>
</html>
