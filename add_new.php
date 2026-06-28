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

    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $filename = time().'_'.basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$filename);
    } else {
        $filename = null;
    }

    $stmt = $conn->prepare("INSERT INTO closet_items (user_id, photo, couleur, tissu, categorie) VALUES (?,?,?,?,?)");
    if($stmt === false){
        die("Erreur prepare : ".$conn->error);
    }
    $stmt->bind_param("issss", $user_id, $filename, $color, $fabric, $category);
    $stmt->execute();
    $stmt->close();

    $insert_msg = "✔ تم حفظ القطعة بنجاح";
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

<!-- BOOTSTRAP -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f5f5f0; /* beige clair */
    font-family: 'Tahoma';
}

.container-box{
    max-width:750px;
    margin:40px auto;
    background:white;
    border-radius:20px;
    padding:35px;
    box-shadow:0 6px 20px rgba(0,0,0,0.12);
    position:relative;
}

/* Title */
h2{
    text-align:center;
    color:#D4AF37; /* gold */
    margin-bottom:25px;
    font-weight:bold;
}

/* Inputs */
input, select{
    border-radius:10px !important;
    padding:12px !important;
    font-size:15px;
    border:1px solid #D4AF37;
}

/* Next Button */
.btn-main{
    width:100%;
    background:#D4AF37; /* gold */
    color:black;
    padding:14px;
    border-radius:12px;
    font-size:17px;
    border:none;
    margin-top:15px;
}
.btn-main:hover{
    background:#E6C97C; /* beige doré */
}

/* Question Box */
.question-box{
    background:#fff8e1; /* beige clair */
    padding:15px;
    border-radius:12px;
    border:1px solid #E6C97C;
    margin-top:15px;
}

/* Result */
#result{
    margin-top:20px;
    padding:15px;
    border-radius:12px;
    font-weight:bold;
    font-size:18px;
}
.good{ background:#d6f8d6; color:#1a7f1a; }
.bad{ background:#ffd6d6; color:#a30000; }

/* Save Button */
#insertBtn{
    display:none;
    margin-top:15px;
}

/* Icon circle */
.icon-circle{
    width:55px;
    height:55px;
    background:#D4AF37; /* gold */
    border-radius:50%;
    position:absolute;
    top:-18px;
    right:-18px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    font-size:26px;
    border:4px solid white;
    box-shadow:0 4px 10px rgba(0,0,0,0.25);
}
</style>
</head>

<body>

<div class="container-box">

    <div class="icon-circle">
        <i class="bi bi-stars"></i>
    </div>

<h2>تحليل ذكـي قبل الشراء</h2>

<form method="POST" enctype="multipart/form-data" id="closetForm">

    <label class="mt-2">صورة القطعة:</label>
    <input type="file" class="form-control" name="photo" required>

    <label class="mt-3">اللون:</label>
    <select name="color" class="form-control" required>
        <option value="">اختر اللون</option>
        <option value="فاتح">فاتح</option>
        <option value="غامق">غامق</option>
        <option value="ملون">ملون</option>
        <option value="ذهبي">ذهبي</option>
        <option value="بيج">بيج</option>
        <option value="أسود">أسود</option>
        <option value="أبيض">أبيض</option>
    </select>

    <label class="mt-3">نوع القماش:</label>
    <select name="fabric" class="form-control" required>
        <option value="">اختر نوع القماش</option>
        <option value="قطن">قطن</option>
        <option value="جينز">جينز</option>
        <option value="حرير">حرير</option>
        <option value="صوف">صوف</option>
        <option value="كتان">كتان</option>
    </select>

    <label class="mt-3">الفئة:</label>
    <select name="category" class="form-control" required>
        <option value="">اختر الفئة</option>
        <option value="قميص">قميص</option>
        <option value="فستان">فستان</option>
        <option value="بنطال">بنطال</option>
        <option value="جاكيت">جاكيت</option>
        <option value="تنورة">تنورة</option>
        <option value="أكسسوارات">أكسسوارات</option>
    </select>

    <button type="button" class="btn-main" onclick="showQuestions()">التالي</button>

    <div id="questions" style="display:none;">

        <!-- Q1 -->
        

    <!-- Q1 -->
    <div class="question-box">
        هل يمكنكِ تخيل 5 إطلالات مختلفة فوراً؟
        <br>
        <input type="radio" name="q1" value="30"> نعم  
        <input type="radio" name="q1" value="0"> لا
    </div>

    <!-- Q2 -->
    <div class="question-box">
        هل ستبقى القطعة مناسبة لأسلوبكِ بعد سنة؟
        <br>
        <input type="radio" name="q2" value="20"> نعم  
        <input type="radio" name="q2" value="0"> لا
    </div>

    <!-- Q3 -->
    <div class="question-box">
        هل لديكِ مناسبة أو سبب فعلي لشراء هذه القطعة هذا الشهر؟
        <br>
        <input type="radio" name="q3" value="20"> نعم  
        <input type="radio" name="q3" value="0"> لا
    </div>

    <!-- Q4 -->
    <div class="question-box">
        هل يمكن ارتداء القطعة في 3 استعمالات مختلفة؟ 
        <span style="color:#b48a00; font-size:14px;">(سهرة – مناسبة – يومي)</span>
        <br>
        <input type="radio" name="q4" value="15"> نعم  
        <input type="radio" name="q4" value="0"> لا
    </div>

    <!-- Q5 -->
    <div class="question-box">
        هل ترين أن السعر مناسب مقارنة بفائدة القطعة؟
        <br>
        <input type="radio" name="q5" value="15"> نعم  
        <input type="radio" name="q5" value="0"> لا
    </div>

    <button type="button" class="btn-main" onclick="calculate()">تحليل النتيجة</button>
</div>


    <div id="result"></div>

    <input type="hidden" name="score" id="scoreInput">
    <button type="submit" class="btn-main" name="insert" id="insertBtn">💾 حفظ القطعة</button>
</form>

<?php if(isset($insert_msg)){ ?>
    <div class="alert alert-success mt-3 text-center"><?= $insert_msg ?></div>
<?php } ?>

</div>


<script>
function showQuestions(){
    document.getElementById("questions").style.display = "block";
}

function calculate(){
    let radios = document.querySelectorAll("#questions input[type='radio']:checked");
    let score = 0;
    radios.forEach(r => score += parseInt(r.value));

    document.getElementById("scoreInput").value = score;

    let result = document.getElementById("result");
    document.getElementById("insertBtn").style.display = "block";

    if(score >= 60){
        result.className = "good";
        result.innerHTML = "✔ نعم، قطعة تستحق الشراء!";
    } else {
        result.className = "bad";
        result.innerHTML = "✖ الأفضل عدم شرائها.";
    }
}
</script>

</body>
</html>
