<?php
session_start();
include "config.php";
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// جلب جميع القطع
$sql = "SELECT * FROM closet_items WHERE user_id=? ORDER BY date_added DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while($row=$result->fetch_assoc()) $items[]=$row;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analyse de Closet</title>
<style>
body{font-family:Tahoma;background:#fff0f5;padding:20px;}
h2{text-align:center;color:#d6336c;}
.card{background:white;padding:10px;margin:10px 0;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);cursor:pointer;display:flex;align-items:center;}
.card img{width:80px;height:80px;border-radius:10px;margin-left:15px;object-fit:cover;}
.details{flex:1;}
#questionsBox{display:none;background:white;padding:15px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-top:20px;}
.result{margin-top:15px;font-weight:bold;padding:10px;border-radius:10px;}
.result.rearrange{background:#fff3cd;color:#856404;}
.result.modify{background:#cce5ff;color:#004085;}
.result.donate{background:#f8d7da;color:#721c24;}
</style>
</head>
<body>

<h2>تحليل القطع</h2>

<div id="itemsContainer">
<?php
if(count($items)>0){
    foreach($items as $row){
        echo '<div class="card" data-id="'.$row['id'].'">';
        echo '<img src="uploads/'.$row['photo'].'" alt="صورة القطعة">';
        echo '<div class="details">';
        echo '<p>الفئة: '.$row['categorie'].'</p>';
        echo '<p>اللون: '.$row['couleur'].'</p>';
        echo '<p>القماش: '.$row['tissu'].'</p>';
        echo '</div></div>';
    }
}else{
    echo "<p style='text-align:center;font-weight:bold;'>لا توجد قطع لتحليلها.</p>";
}
?>
</div>

<div id="questionsBox">
<h3 id="itemTitle">تحليل القطعة</h3>
<div class="question"><p>1. هل تعتقدين أن القطعة مرتبطة بأسلوب قديم؟</p><input type="checkbox" value="1"></div>
<div class="question"><p>2. هل ستكون اختيارك الأول أو الثاني للخروج بسرعة؟</p><input type="checkbox" value="1"></div>
<div class="question"><p>3. هل تتطلب جهداً أكبر للعناية بها؟</p><input type="checkbox" value="1"></div>
<div class="question"><p>4. هل تحتاج تعديل مقاس صغير؟</p><input type="checkbox" value="1"></div>
<div class="question"><p>5. هل القطعة منعزلة في خزانتك؟</p><input type="checkbox" value="1"></div>
<button onclick="analyzeItem()">تحليل</button>
<div id="analysisResult"></div>
</div>

<script>
let selectedItemId = null;
const cards=document.querySelectorAll('.card');
const questionsBox=document.getElementById('questionsBox');
const resultDiv=document.getElementById('analysisResult');

cards.forEach(card=>{
    card.addEventListener('click',()=>{
        selectedItemId=card.getAttribute('data-id');
        questionsBox.style.display='block';
        resultDiv.innerHTML='';
        questionsBox.querySelectorAll('input[type="checkbox"]').forEach(cb=>cb.checked=false);
        let title=card.querySelector('.details p').innerText;
        document.getElementById('itemTitle').innerText='تحليل القطعة: '+title;
        window.scrollTo({top:questionsBox.offsetTop-20,behavior:'smooth'});
    });
});

function analyzeItem(){
    const checkboxes=questionsBox.querySelectorAll('input[type="checkbox"]');
    let score=0;
    checkboxes.forEach(cb=>{if(cb.checked) score++;});

    let recommendation='',cls='';
    if(score<=1){recommendation='💡 يمكن إعادة ترتيبها'; cls='rearrange';}
    else if(score<=3){recommendation='🛠 قد تحتاج تعديل صغير'; cls='modify';}
    else{recommendation='🎁 من الأفضل التبرع بها'; cls='donate';}

    resultDiv.innerHTML=`<div class="result ${cls}">${recommendation}</div>`;
}
</script>

</body>
</html>
