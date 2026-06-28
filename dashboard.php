<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* TOTAL ITEMS */
$q_total = $conn->query("SELECT COUNT(*) AS total FROM closet_items WHERE user_id=$user_id");
$total_items = $q_total->fetch_assoc()['total'];

/* ITEMS PER CATEGORY */
$q_cat = $conn->query("SELECT categorie, COUNT(*) AS total FROM closet_items WHERE user_id=$user_id GROUP BY categorie");

/* ITEMS PER COLOR */
$q_color = $conn->query("SELECT couleur, COUNT(*) AS total FROM closet_items WHERE user_id=$user_id GROUP BY couleur");

/* ITEMS PER FABRIC */
$q_fabric = $conn->query("SELECT tissu, COUNT(*) AS total FROM closet_items WHERE user_id=$user_id GROUP BY tissu");

/* ITEMS ADDED THIS MONTH */
$q_month = $conn->query("
    SELECT COUNT(*) AS total 
    FROM closet_items 
    WHERE user_id=$user_id 
    AND MONTH(date_added)=MONTH(CURRENT_DATE()) 
    AND YEAR(date_added)=YEAR(CURRENT_DATE())
");
$month_items = $q_month->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>لوحة التحكم - Closet</title>

<!-- BOOTSTRAP & ICONS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background:#f5f0e6; /* beige clair */
    font-family:'Tajawal', sans-serif;
    margin:0;
    padding:0;
}

.container{
    margin-top:30px;
}

.btn-home{
    background:#d6bfae; /* beige plus foncé */
    color:black;
    font-weight:bold;
    padding:10px 25px;
    border-radius:12px;
    margin-bottom:25px;
    display:inline-block;
    transition:0.3s;
}
.btn-home:hover{
    transform:translateY(-3px);
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

/* DASHBOARD BOX */
.dashboard-box{
    background:#e8e4dd; /* gris clair */
    color:black;
    border-radius:15px;
    padding:25px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition: transform 0.3s, box-shadow 0.3s;
}
.dashboard-box:hover{
    transform:translateY(-5px) scale(1.02);
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

.dashboard-icon{
    font-size:40px;
    margin-left:15px;
    opacity:0.85;
    transition: transform 0.3s;
}
.dashboard-box:hover .dashboard-icon{
    transform: rotate(15deg);
}

.dashboard-content .title{
    font-size:18px;
    font-weight:bold;
}

.dashboard-content .value{
    font-size:28px;
    font-weight:bold;
    margin-top:5px;
}

.section-title{
    font-size:20px;
    font-weight:bold;
    color:#a67856; /* couleur beige foncé */
    margin:20px 0 10px 0;
}
</style>
</head>
<body>

<div class="container text-center">

    <!-- BOUTON HOME -->
    <a href="home.php" class="btn-home"><i class="bi bi-house-door-fill"></i> العودة إلى الصفحة الرئيسية</a>

    <h2 class="mt-2 mb-4" style="color:#a67856;font-weight:bold;">لوحة التحكم</h2>

    <!-- TOTAL & MONTH -->
    <div class="row">
        <div class="col-md-6">
            <div class="dashboard-box">
                <div class="dashboard-content">
                    <div class="title">عدد القطع في الخزانة</div>
                    <div class="value"><?= $total_items ?></div>
                </div>
                <i class="bi bi-collection dashboard-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-box">
                <div class="dashboard-content">
                    <div class="title">القطع المضافة هذا الشهر</div>
                    <div class="value"><?= $month_items ?></div>
                </div>
                <i class="bi bi-calendar-event dashboard-icon"></i>
            </div>
        </div>
    </div>

    <!-- CATEGORY -->
    <div class="section-title">عدد القطع حسب الفئة</div>
    <div class="row">
        <?php while($cat = $q_cat->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="dashboard-box">
                <div class="dashboard-content">
                    <div class="title"><?= $cat['categorie'] ?></div>
                    <div class="value"><?= $cat['total'] ?></div>
                </div>
                <i class="bi bi-tags dashboard-icon"></i>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- COLOR -->
    <div class="section-title">عدد القطع حسب اللون</div>
    <div class="row">
        <?php while($color = $q_color->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="dashboard-box">
                <div class="dashboard-content">
                    <div class="title"><?= $color['couleur'] ?></div>
                    <div class="value"><?= $color['total'] ?></div>
                </div>
                <i class="bi bi-palette dashboard-icon"></i>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- FABRIC -->
    <div class="section-title">عدد القطع حسب نوع القماش</div>
    <div class="row">
        <?php while($fabric = $q_fabric->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="dashboard-box">
                <div class="dashboard-content">
                    <div class="title"><?= $fabric['tissu'] ?></div>
                    <div class="value"><?= $fabric['total'] ?></div>
                </div>
                <i class="bi bi-bootstrap-dashboard dashboard-icon"></i>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
