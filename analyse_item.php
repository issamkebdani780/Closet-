<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_GET['id']);

// Récupération de la pièce
$sql = "SELECT * FROM closet_items WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$item){
    header("Location: my_closet_expert.php");
    exit();
}

$recommendation_msg = "";
$recommendation_ar  = "";

if($_SERVER['REQUEST_METHOD']=='POST'){

    // Récupération Oui/Non (1 ou 0)
    $q1 = isset($_POST['q1']) ? intval($_POST['q1']) : 0;
    $q2 = isset($_POST['q2']) ? intval($_POST['q2']) : 0;
    $q3 = isset($_POST['q3']) ? intval($_POST['q3']) : 0;
    $q4 = isset($_POST['q4']) ? intval($_POST['q4']) : 0;
    $q5 = isset($_POST['q5']) ? intval($_POST['q5']) : 0;

    $score = $q1 + $q2 + $q3 + $q4 + $q5;

    if($score <= 1){
        $recommendation = "Rearrange";
        $recommendation_ar = "💡 يمكن إعادة ترتيبها";
    }
    elseif($score <= 3){
        $recommendation = "Modify";
        $recommendation_ar = "🛠 قد تحتاج تعديل صغير";
    }
    else{
        $recommendation = "Donate";
        $recommendation_ar = "🎁 من الأفضل التبرع بها";
    }

    $sql = "INSERT INTO closet_analysis (item_id,user_id,q1,q2,q3,q4,q5,score,recommendation)
            VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiiiss", $item_id, $user_id, $q1, $q2, $q3, $q4, $q5, $score, $recommendation);
    $stmt->execute();
    $stmt->close();

    $recommendation_msg = "✅ تم حفظ التحليل: <b>$recommendation_ar</b>";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تحليل القطعة</title>

<style>
body{
    font-family:Tahoma, sans-serif;
    background:#f5f5f0;
    padding:20px;
}
h2{
    text-align:center;
    color:#D4AF37;
    margin-bottom:20px;
}
form{
    max-width:600px;
    margin:auto;
}
.question{
    background:#fff8e1;
    padding:15px;
    margin:10px 0;
    border-radius:12px;
    border:1px solid #E6C97C;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}
.question p{
    margin:0 0 5px 0;
}
.radio-box{
    display:flex;
    gap:25px;
    margin-top:8px;
}
.radio-box label{
    cursor:pointer;
}
button{
    padding:12px;
    background:#D4AF37;
    color:black;
    border:none;
    border-radius:12px;
    cursor:pointer;
    width:100%;
    font-size:16px;
    transition:0.3s;
}
button:hover{
    background:#E6C97C;
}
.msg{
    margin:15px 0;
    text-align:center;
    padding:10px;
    background:#fff3cd;
    color:#856404;
    border-radius:10px;
    font-weight:bold;
}
.result{
    margin-top:15px;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    color:#333;
}
</style>
</head>

<body>

<h2>تحليل القطعة: <?php echo $item['categorie'].' - '.$item['couleur']; ?></h2>

<?php if($recommendation_msg) echo "<div class='msg'>$recommendation_msg</div>"; ?>

<form method="POST">

    <div class="question">
        <p>1. هل تعتقد أن القطعة مرتبطة بأسلوب قديم؟</p>
        <div class="radio-box">
            <label><input type="radio" name="q1" value="1"> نعم</label>
            <label><input type="radio" name="q1" value="0" checked> لا</label>
        </div>
    </div>

    <div class="question">
        <p>2. هل ستكون اختيارك الأول أو الثاني للخروج بسرعة؟</p>
        <div class="radio-box">
            <label><input type="radio" name="q2" value="1"> نعم</label>
            <label><input type="radio" name="q2" value="0" checked> لا</label>
        </div>
    </div>

    <div class="question">
        <p>3. هل تتطلب جهداً أكبر للعناية بها؟</p>
        <div class="radio-box">
            <label><input type="radio" name="q3" value="1"> نعم</label>
            <label><input type="radio" name="q3" value="0" checked> لا</label>
        </div>
    </div>

    <div class="question">
        <p>4. هل تحتاج تعديل مقاس صغير؟</p>
        <div class="radio-box">
            <label><input type="radio" name="q4" value="1"> نعم</label>
            <label><input type="radio" name="q4" value="0" checked> لا</label>
        </div>
    </div>

    <div class="question">
        <p>5. هل القطعة منعزلة في خزانتك؟</p>
        <div class="radio-box">
            <label><input type="radio" name="q5" value="1"> نعم</label>
            <label><input type="radio" name="q5" value="0" checked> لا</label>
        </div>
    </div>

    <button type="submit">تحليل وحفظ</button>
</form>

<?php if($recommendation_msg): ?>
<div class="result">
    التوصية النهائية: <?php echo $recommendation_ar; ?>
</div>
<?php endif; ?>

</body>
</html>
