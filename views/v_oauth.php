<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<title>OAuth Test</title>
<meta name="robots" content="noindex" />
<link rel="stylesheet" href="/media/css/style-min.css" />
<link rel="shortcut icon" href="/favicon.ico">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if lte IE 7]><script src="js/IE8.js" type="text/javascript"></script><![endif]-->
<!--[if lt IE 7]><link rel="stylesheet" type="text/css" media="all" href="css/ie6.css"/><![endif]-->
<style type="text/css">
nav ul li { display:inline}
header {border-bottom:2px solid #555;padding-top:1em}
footer {border-top:2px solid #555;padding-top:1em}
section {background:#eee}
</style>
</head>
<body><div class="page">
<header>
    <div style="width:90%;float:right">
    <h1>OAuth 2.0 Test</h1>
    <nav>
      <ul>
        <li><a href="/client/index">Client</a></li>
        <li><a href="/server/index">Server</a></li>
        <li><a href="/api/index">API Resources</a></li>
      </ul>
    </nav></div>
    <div class="date">
    <span class="day"><?php echo date('d'); ?></span>
    <span class="month"><?php echo date('F'); ?></span>
    <span class="year"><?php echo date('Y'); ?></span>
    </div>
</header><section id="main"><?php echo $content;?></section>
<footer>
  <section id="extras" class="body">
  </section><!-- /#extras -->
  <nav>
  </nav>
  <address id="about" class="vcard body">
    <span class="primary">
        <strong><a href="#" class="fn url">OALite Inc.</a></strong>
        <span class="role">Open Application Lite</span>
    </span><!-- /.primary -->
    <img src="images/avatar.gif" alt="Open Application Lite Logo" class="photo" />
    <span class="bio">Open Application Lite is a website that offers online software serivce to personal and medium and small-sized enterprises. Its foundation is OALite Inc.</span>
  </address><!-- /#about -->
</footer></div>
</body>
</html>
