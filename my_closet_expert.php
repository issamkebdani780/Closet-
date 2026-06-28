<?php
session_start();
include "config.php";
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// Données utilisateur
$sql_user = "SELECT * FROM users WHERE id=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Pièces de l'utilisateur
$sql = "SELECT * FROM closet_items WHERE user_id=? ORDER BY date_added DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while($row=$result->fetch_assoc()) $items[]=$row;
$stmt->close();

// Supprimer un item si demandé
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $stmt_del = $conn->prepare("DELETE FROM closet_items WHERE id=? AND user_id=?");
    $stmt_del->bind_param("ii",$delete_id,$user_id);
    $stmt_del->execute();
    $stmt_del->close();
    header("Location: my_closet_expert.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>خزانتي - Expert Closet</title>

<style>
body {
    font-family: 'Tajawal', sans-serif;
    background: #f8f9fa; /* très clair, gris/blanc */
    margin: 0;
    padding: 0;
}

/* Header / bouton */
.header-btn {
    display: block;
    width: calc(100% - 40px);
    max-width: 200px;
    margin: 20px auto;
    text-align: center;
    background: #D4AF37;
    color: black;
    font-weight: bold;
    padding: 12px 0;
    border-radius: 15px;
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: 0.3s;
}
.header-btn:hover {
    background: #E6C97C;
}

/* Search bar */
#searchBar {
    width: calc(100% - 40px);
    margin: 10px auto 30px auto;
    padding: 12px 15px;
    border-radius: 15px;
    border: 1px solid #D4AF37;
    display: block;
    max-width: 600px;
    font-size: 16px;
    color: #333;
    background: #fffdf5;
}

/* Grid container */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    padding: 0 20px 40px 20px;
}

/* Card */
.card {
    background: #fffdf5;
    border-radius: 20px;
    padding: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.12);
}

/* Card image */
.card img {
    width: 100%;
    height: 180px;
    border-radius: 15px;
    object-fit: cover;
    margin-bottom: 10px;
    border: 2px solid #D4AF37;
}

/* Card details */
.details p {
    margin: 6px 0;
    font-weight: 500;
    font-size: 14px;
    color: #333;
}

/* Buttons inside card */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    margin: 4px 2px;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
    transition: 0.3s;
    font-weight: bold;
}
.btn.modify {
    background: #D4AF37;
    color: black;
}
.btn.modify:hover {
    background: #E6C97C;
}
.btn.delete {
    background: #f5f0e0;
    color: #a70000;
}
.btn.delete:hover {
    background: #f0e3c6;
}

/* Last result */
.result {
    padding: 8px;
    border-radius: 12px;
    font-weight: bold;
    text-align: center;
    margin-top: 6px;
    font-size: 13px;
    border-left: 5px solid #D4AF37;
}

/* Responsive */
@media(max-width:600px){
    #searchBar { width: calc(100% - 30px); }
    .grid-container { grid-template-columns: 1fr; }
}
</style>

</head>
<body>

<a href="home.php" class="header-btn">🏠 العودة إلى الرئيسية</a>

<input type="text" id="searchBar" placeholder="ابحث باللون، الفئة، القماش، الجنس..." onkeyup="filterCards()">

<div class="grid-container" id="itemsContainer">
<?php
if(count($items)>0){
    foreach($items as $row){
        // Récupération de la dernière analyse
        $stmt_analysis = $conn->prepare("SELECT recommendation FROM closet_analysis WHERE item_id=? ORDER BY date_analyzed DESC LIMIT 1");
        $stmt_analysis->bind_param("i",$row['id']);
        $stmt_analysis->execute();
        $result_analysis = $stmt_analysis->get_result()->fetch_assoc();
        $stmt_analysis->close();
        $last_result = isset($result_analysis['recommendation']) ? $result_analysis['recommendation'] : 'لم يتم التحليل بعد';

        // Définir couleur selon la recommandation
        switch($last_result){
            case "Rearrange":
            case "💡 يمكن إعادة ترتيبها":
                $color_result = "#fff8dc"; // beige clair
                $last_result_ar = "💡 يمكن إعادة ترتيبها";
                break;
            case "Modify":
            case "🛠 قد تحتاج تعديل صغير":
                $color_result = "#ffe5b4"; // orange clair
                $last_result_ar = "🛠 قد تحتاج تعديل صغير";
                break;
            case "Donate":
            case "🎁 من الأفضل التبرع بها":
                $color_result = "#d4edda"; // vert clair
                $last_result_ar = "🎁 من الأفضل التبرع بها";
                break;
            default:
                $color_result = "#e0e0e0"; // gris clair
                $last_result_ar = "لم يتم التحليل بعد";
        }

        echo '<div class="card" data-categorie="'.$row['categorie'].'" data-couleur="'.$row['couleur'].'" data-tissu="'.$row['tissu'].'" data-gender="'.$row['gender'].'" data-event="'.$row['event'].'" data-id="'.$row['id'].'">';
        echo '<img src="uploads/'.$row['photo'].'" alt="صورة القطعة">';
        echo '<div class="details">';
        echo '<p>الفئة: '.$row['categorie'].'</p>';
        echo '<p>اللون: '.$row['couleur'].'</p>';
        echo '<p>القماش: '.$row['tissu'].'</p>';
        echo '<div class="result" style="background:'.$color_result.'">'.$last_result_ar.'</div>';
        echo '<a href="analyse_item.php?id='.$row['id'].'" class="btn modify">تحليل/تعديل</a>';
        echo '<a href="my_closet_expert.php?delete_id='.$row['id'].'" class="btn delete" onclick="return confirm(\'هل تريد حذف هذه القطعة؟\')">حذف</a>';
        echo '</div></div>';
    }
}else{
    echo "<p style='text-align:center;font-weight:bold;color:#555;'>لا توجد قطع.</p>";
}
?>
</div>

<script>
function filterCards(){
    const input=document.getElementById('searchBar').value.toLowerCase();
    const cards=document.querySelectorAll('.grid-container .card');
    cards.forEach(card=>{
        const categorie=card.getAttribute('data-categorie').toLowerCase();
        const couleur=card.getAttribute('data-couleur').toLowerCase();
        const tissu=card.getAttribute('data-tissu').toLowerCase();
        const gender=card.getAttribute('data-gender').toLowerCase();
        const event=card.getAttribute('data-event').toLowerCase();
        if(categorie.includes(input)||couleur.includes(input)||tissu.includes(input)||gender.includes(input)||event.includes(input)){
            card.style.display='block';
        }else{
            card.style.display='none';
        }
    });
}
</script>

</body>
</html>
