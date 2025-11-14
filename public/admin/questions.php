<?php
require_once __DIR__.'/../../config/db.php'; require_admin();

$exam_id = intval($_GET['exam_id'] ?? $_POST['exam_id'] ?? 0);
if(!$exam_id){ header('Location:/admin/exams.php'); exit; }

// CSV import
if(isset($_POST['import_csv']) && isset($_FILES['csv_file'])){
    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');
    $header = fgetcsv($handle);
    $added = 0; $skipped = 0;
    while(($row = fgetcsv($handle)) !== false){
        $q = trim($row[0] ?? ''); $opts = array_slice($row,1,4);
        $ci = intval($row[5] ?? 0); $img = trim($row[6] ?? '');
        if(!$q){ $skipped++; continue; }
        $options_json = json_encode(array_map('trim',$opts));
        $pdo->prepare('INSERT INTO questions (exam_id,question_text,options,correct_index,question_image) VALUES (?,?,?,?,?)')->execute([$exam_id,$q,$options_json,$ci,$img?:null]);
        $added++;
    }
    fclose($handle);
    $msg = "Imported: $added, Skipped: $skipped";
}

// Add question with uploads
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_question'){
    $question_text = trim($_POST['question_text'] ?? '');
    $opts = [trim($_POST['o1'] ?? ''), trim($_POST['o2'] ?? ''), trim($_POST['o3'] ?? ''), trim($_POST['o4'] ?? '')];
    $correct_index = intval($_POST['correct_index'] ?? 0);

    $uploadDir = __DIR__ . '/../../public/uploads/questions/';
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $qimgPath = null;
    if(!empty($_FILES['question_image']['tmp_name'])){
        $ext = pathinfo($_FILES['question_image']['name'], PATHINFO_EXTENSION);
        $fn = 'q_'.time().'_'.bin2hex(random_bytes(5)).'.'.$ext;
        move_uploaded_file($_FILES['question_image']['tmp_name'], $uploadDir.$fn);
        $qimgPath = '/uploads/questions/'.$fn;
    }

    $option_images = [];
    for($i=1;$i<=4;$i++){
        if(!empty($_FILES['opt'.$i]['tmp_name'])){
            $ext = pathinfo($_FILES['opt'.$i]['name'], PATHINFO_EXTENSION);
            $fn = 'o_'.time().'_'.$i.'_'.bin2hex(random_bytes(5)).'.'.$ext;
            move_uploaded_file($_FILES['opt'.$i]['tmp_name'], $uploadDir.$fn);
            $option_images[] = '/uploads/questions/'.$fn;
        } else {
            $option_images[] = null;
        }
    }

    $pdo->prepare('INSERT INTO questions (exam_id, question_text, question_image, option_images, options, correct_index) VALUES (?,?,?,?,?,?)')
        ->execute([$exam_id, $question_text, $qimgPath, json_encode($option_images), json_encode($opts), $correct_index]);

    header('Location: /admin/questions.php?exam_id='.$exam_id);
    exit;
}

// fetch questions
$stmt = $pdo->prepare('SELECT * FROM questions WHERE exam_id = ? ORDER BY id DESC');
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$examStmt = $pdo->prepare('SELECT * FROM exams WHERE id=?'); $examStmt->execute([$exam_id]); $exam = $examStmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__.'/../../includes/header.php'; require_once __DIR__.'/_side_nav.php';
?>
<h3>Questions for: <?php echo e($exam['title']); ?></h3>
<?php if(!empty($msg)): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="mb-3 card p-3">
  <h5>Import CSV</h5>
  <input type="file" name="csv_file" accept=".csv" required>
  <button name="import_csv" class="btn btn-primary mt-2">Import CSV</button>
  <p class="text-muted">CSV columns: question_text, option1, option2, option3, option4, correct_index (0-3), question_image(optional URL)</p>
</form>

<div class="mb-3">
<?php foreach($questions as $q): $opts = json_decode($q['options'], true); $optImgs = json_decode($q['option_images'] ?? '[]', true); ?>
  <div class="card mb-2 p-3">
    <div class="d-flex justify-content-between"><div><strong><?php echo e($q['question_text']); ?></strong></div><div><small class="small-muted"><?php echo e($q['created_at'] ?? $q['id']); ?></small></div></div>
    <?php if(!empty($q['question_image'])): ?><div class="mt-2"><img src="<?php echo e($q['question_image']); ?>" style="max-width:220px;"></div><?php endif; ?>
    <ol class="mt-2">
      <?php foreach($opts as $i=>$o): ?><li><?php echo htmlspecialchars($o); if(!empty($optImgs[$i])): ?><div><img src="<?php echo e($optImgs[$i]); ?>" style="max-width:140px;"></div><?php endif; if($i==(int)$q['correct_index']): ?> <span class="badge bg-success ms-2">Correct</span><?php endif; ?></li><?php endforeach; ?>
    </ol>
  </div>
<?php endforeach; ?>
</div>

<h5 class="mt-4">Add Question</h5>
<form method="post" enctype="multipart/form-data" class="card p-3">
  <input type="hidden" name="action" value="add_question">
  <input type="hidden" name="exam_id" value="<?php echo e($exam_id); ?>">
  <div class="mb-2"><label>Question</label><textarea name="question_text" class="form-control" required></textarea></div>
  <div class="mb-2"><label>Question Image (optional)</label><input type="file" name="question_image" class="form-control"></div>
  <div class="row"><div class="col"><input name="o1" class="form-control" placeholder="Option 1"></div><div class="col"><input name="o2" class="form-control" placeholder="Option 2"></div></div>
  <div class="row mt-2"><div class="col"><input name="o3" class="form-control" placeholder="Option 3"></div><div class="col"><input name="o4" class="form-control" placeholder="Option 4"></div></div>
  <div class="row mt-2"><div class="col"><label>Option images</label><input type="file" name="opt1" class="form-control"></div><div class="col"><input type="file" name="opt2" class="form-control"></div><div class="col"><input type="file" name="opt3" class="form-control"></div><div class="col"><input type="file" name="opt4" class="form-control"></div></div>
  <div class="mt-2"><label>Correct option index (0-3)</label><input type="number" name="correct_index" class="form-control" min="0" max="3" value="0"></div>
  <button class="btn btn-success mt-3">Add Question</button>
</form>

<?php echo '</main></div>'; require_once __DIR__.'/../../includes/footer.php'; ?>