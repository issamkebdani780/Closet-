<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['insert'])) {

    $color = $_POST['color'];
    $fabric = $_POST['fabric'];
    $category = $_POST['category'];

    // Gestion du fichier photo
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $filename = time().'_'.basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$filename);
    } else {
        $filename = null;
    }

    // Insertion de la pièce
    $stmt = $conn->prepare("INSERT INTO closet_items (user_id, photo, couleur, tissu, categorie) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss", $user_id, $filename, $color, $fabric, $category);
    $stmt->execute();
    $item_id = $conn->insert_id;
    $stmt->close();

    // Récupération des scores des questions
    $q1 = isset($_POST['q1']) ? $_POST['q1'] : 0;
    $q2 = isset($_POST['q2']) ? $_POST['q2'] : 0;
    $q3 = isset($_POST['q3']) ? $_POST['q3'] : 0;
    $q4 = isset($_POST['q4']) ? $_POST['q4'] : 0;
    $q5 = isset($_POST['q5']) ? $_POST['q5'] : 0;
    $score = $_POST['score'];

    // Détermination de la recommandation selon le score
    if($score <= 20){
        $recommendation = "Rearrange";
        $recommendation_ar = "💡 يمكن إعادة ترتيبها";
    }
    elseif($score <= 60){
        $recommendation = "Modify";
        $recommendation_ar = "🛠 قد تحتاج تعديل صغير";
    }
    else{
        $recommendation = "Donate";
        $recommendation_ar = "🎁 من الأفضل التبرع بها";
    }

    // Enregistrement de l'analyse
    $stmt2 = $conn->prepare("INSERT INTO closet_analysis (item_id, user_id, q1, q2, q3, q4, q5, score, recommendation) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt2->bind_param("iiiiiiiss",$item_id,$user_id,$q1,$q2,$q3,$q4,$q5,$score,$recommendation);
    $stmt2->execute();
    $stmt2->close();

    $insert_msg = "✔ تم حفظ النتيجة بنجاح";
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تحليل القطعة</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{ background:#f5f5f0; font-family: 'Tahoma'; }
.container-box{ max-width:750px; margin:40px auto; background:white; border-radius:20px; padding:35px; box-shadow:0 6px 20px rgba(0,0,0,0.12); position:relative; }
h2{ text-align:center; color:#D4AF37; margin-bottom:25px; font-weight:bold; }
input, select{ border-radius:10px !important; padding:12px !important; font-size:15px; border:1px solid #D4AF37; }
.btn-main{ width:100%; background:#D4AF37; color:black; padding:14px; border-radius:12px; font-size:17px; border:none; margin-top:15px; }
.btn-main:hover{ background:#E6C97C; }
.question-box{ background:#fff8e1; padding:15px; border-radius:12px; border:1px solid #E6C97C; margin-top:15px; }
.option-wrapper{ display:flex; align-items:center; margin-top:8px; }
.option-wrapper input{ margin-left:10px; margin-right:10px; vertical-align:middle; }
#result{ margin-top:20px; padding:15px; border-radius:12px; font-weight:bold; font-size:18px; }
.good{ background:#d6f8d6; color:#1a7f1a; }
.bad{ background:#ffd6d6; color:#a30000; }
#insertBtn{ display:none; margin-top:15px; }
.icon-circle{ width:55px; height:55px; background:#D4AF37; border-radius:50%; position:absolute; top:-18px; right:-18px; display:flex; justify-content:center; align-items:center; color:white; font-size:26px; border:4px solid white; box-shadow:0 4px 10px rgba(0,0,0,0.25); }
@media(max-width:600px){ .container-box{ padding:20px; margin:20px; } button{ font-size:16px; padding:12px; } }
</style>
</head>
<body>

<div class="container-box">

<div class="icon-circle"><i class="bi bi-stars"></i></div>
<h2>تحليل ذكاء الخزانة</h2>

<form method="POST" enctype="multipart/form-data" id="closetForm">

    <label class="mt-2">📸 صورة القطعة :</label>
    <input type="file" class="form-control" name="photo" required>

    <label class="mt-3">🎨 اللون :</label>
    <select name="color" class="form-control" required>
        <option value="">اختاري</option>
        <option value="فاتح">فاتح</option>
        <option value="غامق">غامق</option>
        <option value="ملون">ملون</option>
    </select>

    <label class="mt-3">🧵 القماش :</label>
    <select name="fabric" class="form-control" required>
        <option value="">اختاري</option>
        <option value="قطن">قطن</option>
        <option value="صوف">صوف</option>
        <option value="حرير">حرير</option>
        <option value="جينز">جينز</option>
    </select>

    <label class="mt-3">👗 الفئة :</label>
    <select name="category" class="form-control" required>
        <option value="">اختاري</option>
        <option value="قميص">قميص</option>
        <option value="بنطال">بنطال</option>
        <option value="فستان">فستان</option>
        <option value="جاكيت">جاكيت</option>
    </select>

    <button type="button" class="btn-main" onclick="showQuestions()">التالي</button>

  <div id="questions" style="display:none;">

    <!-- Q1 -->
    <div class="question-box">
        <b>هل تعتقدين أن القطعة "مرتبطة" بأسلوب قديم أو مرحلة معينة لم تعد تعبر عنكِ؟</b>
        <div class="option-wrapper"><input type="radio" name="q1" value="0"><label>نعم</label></div>
        <div class="option-wrapper"><input type="radio" name="q1" value="20"><label>لا</label></div>
    </div>

    <!-- Q2 -->
    <div class="question-box">
        <b>إذا عندك 5 دقائق فقط للخروج، هل ستكون هذه القطعة من أول اختياراتك؟</b>
        <div class="option-wrapper"><input type="radio" name="q2" value="20"><label>نعم</label></div>
        <div class="option-wrapper"><input type="radio" name="q2" value="0"><label>لا</label></div>
    </div>

    <!-- Q3 -->
    <div class="question-box">
        <b>هل تتطلب القطعة عناية مزعجة (كيّ، تنظيف مكلف) يجعلك تهربين منها؟</b>
        <div class="option-wrapper"><input type="radio" name="q3" value="0"><label>نعم</label></div>
        <div class="option-wrapper"><input type="radio" name="q3" value="20"><label>لا</label></div>
    </div>

    <!-- Q4 -->
    <div class="question-box">
        <b>هل تحتاج القطعة لتعديل (تضييق، تقصير) لكنك تؤجلين الأمر؟</b>
        <div class="option-wrapper"><input type="radio" name="q4" value="0"><label>نعم</label></div>
        <div class="option-wrapper"><input type="radio" name="q4" value="20"><label>لا</label></div>
    </div>

    <!-- Q5 -->
    <div class="question-box">
        <b>هل تستطيعين تنسيق هذه القطعة بسهولة مع 3 قطع أخرى لديك؟</b>
        <div class="option-wrapper"><input type="radio" name="q5" value="20"><label>نعم</label></div>
        <div class="option-wrapper"><input type="radio" name="q5" value="0"><label>لا</label></div>
    </div>

    <button type="button" class="btn-main" onclick="calculate()">تحليل النتيجة</button>
</div>


    <div id="result"></div>

    <input type="hidden" name="score" id="scoreInput">
    <button type="submit" class="btn-main" name="insert" id="insertBtn">💾 حفظ النتيجة</button>

</form>

<?php if(isset($insert_msg)){ ?>
    <div class="alert alert-success mt-3 text-center"><?= $insert_msg ?></div>
<?php } ?>

</div>

<script>
function showQuestions(){ document.getElementById("questions").style.display = "block"; }

function calculate(){
    let radios = document.querySelectorAll("#questions input[type='radio']:checked");
    let score = 0;
    radios.forEach(r => score += parseInt(r.value));
    document.getElementById("scoreInput").value = score;

    let result = document.getElementById("result");
    document.getElementById("insertBtn").style.display = "block";

    if(score <= 20){
        result.className = "good";
        result.innerHTML = "💡 يمكن إعادة ترتيبها";
    } else if(score <= 60){
        result.className = "bad";
        result.innerHTML = "🛠 قد تحتاج تعديل صغير";
    } else{
        result.className = "bad";
        result.innerHTML = "🎁 من الأفضل التبرع بها";
    }
}
</script>

</body>
</html>
