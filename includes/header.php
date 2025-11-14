<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>CBT v6 Creative</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/creative-theme.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"></head><body>
<div class="nav-3d d-flex align-items-center">
  <div class="brand d-flex align-items-center"><div class="logo-3d"></div><div>CBT v6</div></div>
  <div class="nav-items">
    <a class="nav-btn" href="/"><i class="fa fa-home"></i> <span class="d-none d-md-inline">Home</span></a>
    <a class="nav-btn" href="/user/dashboard.php"><i class="fa fa-graduation-cap"></i> <span class="d-none d-md-inline">Dashboard</span></a>
    <div class="dropdown"><a class="nav-btn dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="fa fa-paint-brush"></i></a>
      <ul class="dropdown-menu dropdown-menu-end p-2">
        <button class="dropdown-item theme-set" data-theme="">Blue</button>
        <button class="dropdown-item theme-set" data-theme="purple">Purple</button>
        <button class="dropdown-item theme-set" data-theme="green">Green</button>
        <button class="dropdown-item theme-set" data-theme="dark">Dark</button>
      </ul></div>
    <?php if(!empty($_SESSION['user'])): ?><a class="nav-btn" href="/logout.php"><i class="fa fa-sign-out-alt"></i></a><?php else: ?><a class="nav-btn" href="/login.php"><i class="fa fa-sign-in-alt"></i></a><?php endif; ?>
  </div>
</div>
<div class="wave-hero"><svg viewBox="0 0 1440 220" preserveAspectRatio="none" style="width:100%;height:220px;display:block"><defs><linearGradient id="g1" x1="0" x2="1"><stop offset="0" stop-color="rgba(78,115,223,0.12)"/><stop offset="1" stop-color="rgba(111,66,193,0.08)"/></linearGradient></defs><path d="M0,80 C240,200 480,10 720,80 C960,150 1200,20 1440,80 L1440 220 L0 220 Z" fill="url(#g1)"><animate attributeName="d" dur="8s" repeatCount="indefinite" values="M0,80 C240,200 480,10 720,80 C960,150 1200,20 1440,80 L1440 220 L0 220 Z;M0,90 C240,160 480,40 720,110 C960,180 1200,60 1440,90 L1440 220 L0 220 Z;M0,80 C240,200 480,10 720,80 C960,150 1200,20 1440,80 L1440 220 L0 220 Z" /></path></svg></div>
<div class="container" style="position:relative; z-index:5; padding-top:28px;">
