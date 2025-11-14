<?php
$side_items = [
  ['url'=>'/admin/dashboard.php','icon'=>'fa-tachometer-alt','label'=>'Dashboard'],
  ['url'=>'/admin/users.php','icon'=>'fa-users','label'=>'Users'],
  ['url'=>'/admin/topics.php','icon'=>'fa-tags','label'=>'Topics'],
  ['url'=>'/admin/exams.php','icon'=>'fa-file-alt','label'=>'Exams'],
  ['url'=>'/admin/questions.php?exam_id=' . ($exam_id ?? ''),'icon'=>'fa-question-circle','label'=>'Questions'],
  ['url'=>'/admin/attempts.php','icon'=>'fa-chart-line','label'=>'Attempts'],
  ['url'=>'/admin/analytics.php','icon'=>'fa-chart-pie','label'=>'Analytics'],
];
?>
<div class="admin-shell"><aside class="side-nav"><?php foreach($side_items as $it): $path = parse_url($it['url'], PHP_URL_PATH); $active = (strpos($_SERVER['REQUEST_URI'], $path) === 0) ? 'active' : ''; if(empty($it['url'])) continue; ?><a href="<?php echo $it['url']; ?>" class="item <?php echo $active; ?>"><i class="fa <?php echo $it['icon']; ?>"></i> <span><?php echo $it['label']; ?></span></a><?php endforeach; ?><hr/><div class="small-muted px-2">Theme</div><div class="d-flex gap-2 px-2 mt-2"><button class="btn btn-sm theme-set" data-theme="">Blue</button><button class="btn btn-sm theme-set" data-theme="purple">Purple</button><button class="btn btn-sm theme-set" data-theme="green">Green</button><button class="btn btn-sm theme-set" data-theme="dark">Dark</button></div></aside><main class="admin-content">
