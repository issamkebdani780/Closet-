<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب بيانات المستخدم
$stmt_user = $conn->prepare("SELECT nom, email, gender FROM users WHERE id=?");
$stmt_user->bind_param("i",$user_id);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$user = $res_user->fetch_assoc();
$stmt_user->close();

// جلب جميع القطع الخاصة بالمستخدم
$sql = "SELECT * FROM closet_items WHERE user_id = ? ORDER BY date_added DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while($row = $result->fetch_assoc()){
    $items[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>خزانتي - My Closet</title>
<style>
body { font-family: Tahoma, sans-serif; background:#fff0f5; margin:0; padding:0; }
.navbar { background:#d6336c; color:white; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; }
.navbar a { color:white; text-decoration:none; margin:0 10px; font-weight:bold; }
.navbar a:hover { text-decoration:underline; }
.profile { background:white; max-width:900px; margin:20px auto; padding:15px 20px; border-radius:15px; box-shadow:0 4px 20px rgba(0,0,0,0.1); display:flex; align-items:center; }
.profile img { width:80px; height:80px; border-radius:50%; object-fit:cover; margin-left:20px; }
.profile-info { flex:1; }
.profile-info h3 { margin:0; color:#d6336c; }
.profile-info p { margin:3px 0; color:#555; }
.container { max-width:900px; margin:auto; padding:0 20px; }
.search-box { margin:20px 0; text-align:center; }
.search-box input { width:80%; padding:10px; border-radius:8px; border:1px solid #ccc; }
.card { display:flex; align-items:center; background:white; padding:15px; margin:10px 0; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.1); transition:0.3s; }
.card:hover { transform:translateY(-5px); box-shadow:0 8px 25px rgba(0,0,0,0.15); }
.card img { width:120px; height:120px; border-radius:15px; object-fit:cover; margin-left:20px; }
.details { flex:1; }
.details p { margin:5px 0; font-weight:bold; }
.score-good { color:#1b8a1b; font-weight:bold; }
.score-bad { color:#a30000; font-weight:bold; }
@media(max-width:700px){ .card { flex-direction:column; align-items:flex-start; } .card img { margin:0 0 10px 0; width:100%; height:auto; } }
</style>
</head>
<body>

<div class="navbar">
    <div>خزانتي</div>
    <div>
        <a href="home.php">تحليل قطعة جديدة</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
</div>

<!-- بطاقة Profile -->
<div class="profile">
    <img src="profile.png" alt="Profile"> <!-- ضع صورة افتراضية أو حسب المستخدم -->
    <div class="profile-info">
        <h3><?php echo $user['nom']; ?></h3>
        <p>البريد: <?php echo $user['email']; ?></p>
        <p>النوع: <?php echo $user['gender']=='male'?'ذكر':'أنثى'; ?></p>
        <p>عدد القطع: <?php echo count($items); ?></p>
    </div>
</div>

<!-- شريط البحث -->
<div class="search-box">
    <input type="text" id="searchInput" placeholder="ابحث في خزانتك باللون، الفئة أو القماش...">
</div>

<!-- عرض القطع -->
<div class="container" id="closetContainer">
<?php
if(count($items) > 0){
    foreach($items as $row){
        $score_class = $row['score'] >= 60 ? "score-good" : "score-bad";
        $score_text = $row['score'] >= 60 ? "مناسبة للشراء" : "غير مناسبة للشراء";
        echo '<div class="card">';
        echo '<img src="uploads/'.$row['photo'].'" alt="صورة القطعة">';
        echo '<div class="details">';
        echo '<p>الفئة: '.$row['categorie'].'</p>';
        echo '<p>اللون: '.$row['couleur'].'</p>';
        echo '<p>نوع القماش: '.$row['tissu'].'</p>';
        echo '<p class="'.$score_class.'">التحليل: '.$score_text.' ('.$row['score'].' نقطة)</p>';
        echo '</div>';
        echo '</div>';
    }
}else{
    echo "<p style='text-align:center; font-weight:bold;'>لا توجد قطع محفوظة بعد.</p>";
}
?>
</div>

<script>
// فلترة القطع حسب البحث
document.getElementById('searchInput').addEventListener('input', function(){
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('#closetContainer .card');
    cards.forEach(card=>{
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
