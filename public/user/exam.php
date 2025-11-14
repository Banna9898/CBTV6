<?php require_once __DIR__.'/../../config/db.php'; require_login();
$exam_id=intval($_GET['exam_id']??0); if(!$exam_id) header('Location:/user/dashboard.php');
$exam=$pdo->prepare('SELECT * FROM exams WHERE id=?'); $exam->execute([$exam_id]); $exam=$exam->fetch(PDO::FETCH_ASSOC);
$qstmt=$pdo->prepare('SELECT * FROM questions WHERE exam_id=? ORDER BY id'); $qstmt->execute([$exam_id]); $questions=$qstmt->fetchAll(PDO::FETCH_ASSOC);
shuffle($questions);
foreach($questions as &$q){ $opts=json_decode($q['options'],true); $paired=[]; foreach($opts as $i=>$o) $paired[]=['idx'=>$i,'text'=>$o]; shuffle($paired); $newOpts=array_map(function($p){return $p['text'];},$paired); $orig=array_map(function($p){return $p['idx'];},$paired); $q['options']=$newOpts; $q['shuffled_correct']=array_search($q['correct_index'],$orig); }
require_once __DIR__.'/../../includes/header.php';
?>
<h3><?php echo e($exam['title']); ?></h3>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div><strong>Question Palette:</strong><div id="qPalette" class="q-palette"></div></div>
  <div class="small-muted">Legend: <span class="badge bg-success">Answered</span> <span class="badge" style="background:#ff6b6b;color:#fff">Review</span> <span class="badge bg-light">Not visited</span></div>
</div>
<form id="examForm" method="post" action="/user/submit_exam.php"><input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
<?php foreach($questions as $i=>$q): ?>
  <div class="card mb-2 p-3">
    <h5>Q<?php echo $i+1; ?>. <?php echo e($q['question_text']); ?></h5>
    <?php if(!empty($q['question_image'])): ?><div><img src="<?php echo e($q['question_image']); ?>" style="max-width:220px;"></div><?php endif; ?>
    <?php foreach($q['options'] as $oi=>$opt): ?><div class="form-check"><input class="form-check-input" type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $oi; ?>" id="q_<?php echo $q['id'].'_'.$oi; ?>"><label class="form-check-label" for="q_<?php echo $q['id'].'_'.$oi; ?>"><?php echo e($opt); ?></label></div><?php endforeach; ?>
    <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-danger mark-review">Mark for review</button></div>
    <input type="hidden" name="correct_<?php echo $q['id']; ?>" value="<?php echo $q['shuffled_correct']; ?>">
  </div>
<?php endforeach; ?>
<button class="btn btn-success">Submit</button></form>
<script src="/assets/js/exam-palette.js"></script>
<?php require_once __DIR__.'/../../includes/footer.php'; ?>