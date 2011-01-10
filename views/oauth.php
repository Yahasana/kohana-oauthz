<?php defined('SYSPATH') or die('No direct script access.');
    $user = Session::instance()->get('user');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<title>OAuth Test</title>
<meta name="robots" content="noindex" />
<link rel="shortcut icon" href="/favicon.ico">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if lte IE 7]><script src="js/IE8.js" type="text/javascript"></script><![endif]-->
<!--[if lt IE 7]><link rel="stylesheet" type="text/css" media="all" href="css/ie6.css"/><![endif]-->
<style type="text/css">
/* http://meyerweb.com/eric/tools/css/reset/ v1.0 | 20080212 */
html,body,div,span,applet,object,iframe,
h1,h2,h3,h4,h5,h6,p,blockquote,pre,
a,abbr,acronym,address,big,cite,code,
del,dfn,em,font,img,ins,kbd,q,s,samp,
small,strike,strong,sub,sup,tt,var,
b,u,i,center,dl,dt,dd,ol,ul,li,
fieldset,form,label,legend,
table,caption,tbody,tfoot,thead,tr,th,td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;vertical-align: baseline;background: transparent;}
body {line-height: 1.129;font-size:100.01%;background:#F5F4EF;  color:#000;  text-align:center;}
ol,ul {list-style: none;}
blockquote,q {quotes: none;}
blockquote:before,blockquote:after,q:before,q:after {content:'';content: none;}
/* remember to define focus styles avoid visible outlines on DIV containers in Webkit browsers! */
div,:focus{outline: 0 none;}
/* remember to highlight inserts somehow! */
ins{text-decoration: none;}
del{text-decoration: line-through;}
/* tables still need 'cellspacing="0"' in the markup */
table {border-collapse: collapse;border-spacing: 0;}
/* Clear borders for <fieldset> and <img> elements */
fieldset,img { border:0 solid; }
/* new standard values for lists, blockquote and cite */
ul,ol,dl{ margin:0 0 1em 1em; } /* LTR */
li { line-height:1.5em; margin-left:0.8em; /* LTR */}
dt { font-weight:bold; }
dd { margin:0 0 1em 0.8em; } /* LTR */

img { -ms-interpolation-mode: bicubic;}
h3{text-shadow:2px 2px 2px #bbb;color:#666;filter:progid:DXImageTransform.Microsoft.Shadow(color=#aaaaaa,Direction=145,Strength=3);height:1%;width:90%;}
h3 a{text-decoration:none}
h3 sup{font-size:8pt;color:#f00}
hr{color: #fff; background:transparent; margin:5px 0;padding:0 0 0 0; border:0; border-bottom: 1px #eee solid;}
* html hr{margin:0 0;line-height:1px;font-size:1px;height:1px;}
pre{
white-space: pre-wrap; /* css-3 */
white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
white-space: -pre-wrap; /* Opera 4-6 */
white-space: -o-pre-wrap; /* Opera 7 */
word-wrap: break-word; /* Internet Explorer 5.5+ */
}
/* HTML5 tags */
header, section, footer, aside, nav, article, figure { display: block;}

/* Clear Floated Elements, http://sonspring.com/journal/clearing-floats */
.clear {clear: both;display: block;overflow: hidden;visibility: hidden;width: 0;height: 0;}
/* http://perishablepress.com/press/2009/12/06/new-clearfix-hack */
.clearfix:after {clear:both;content:' ';display: block;	font-size: 0;line-height: 0;visibility: hidden;width: 0;height: 0;}
/*
	The following zoom:1 rule is specifically for IE6 + IE7.
	Move to separate stylesheet if invalid CSS is a problem.
*/
* html .clearfix,*:first-child+html .clearfix {zoom: 1;}

.page{text-align:left;margin:0 auto;width:1024px;background:#eee}
header, #main, footer { clear:both; }
header{ position:relative;border:1px solid #aaa;border-bottom-radius: 10px;-moz-border-radius-bottomleft: 10px;-moz-border-radius-bottomright:10px;-webkit-border-bottom-radius: 10px;
  color: #000; background:#fff url(/logo.jpg) no-repeat left 100%;
  padding:1.8em 2em 0 0;margin-bottom:10px
}
nav ul li { display:inline}
header {border-bottom:2px solid #555;padding-top:1em}
footer {border-top:2px solid #555;padding-top:1em}
section {background:#eee}

#main{margin:10px auto;display:block;padding:10px 5px}

.date{position:relative;width:66px;padding:45px 5px 0;}
.date .day{line-height:45px;font-size:45px;position:absolute;top:0}
.date .month{font-size:25px;text-transform:uppercase;}
.date .year{display:block; position:absolute;right:-5px;top:15px;
rotation: 90deg !important;
-webkit-transform: rotate(-90deg);
-moz-transform: rotate(-90deg);
-ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}

dl {margin: 20px;border-left: 1px solid #999;padding-left: 10px;}
dt {font-size: 2.0em;margin-bottom: 10px;}
dt span {font-style: italic;font-size: 1.3em;}
dd {font-size: 1.4em;margin-left: 20px;	margin-bottom: 10px;}

footer { border:1px solid #aaa;
border-bottom-radius: 10px;
-moz-border-radius-topleft: 10px;
-moz-border-radius-topright:10px;
-webkit-border-bottom-radius: 10px;padding:10px 5px
}

label{display:inline-block;width:23%;vertical-align:middle}
table{
    border:1px solid #ccc;margin:10px
}
tbody{
    border-top:1px solid #ccc;margin:5px 0
}
th{
    vertical-align: middle; padding:5px 1em
}
</style>
</head>
<body><div class="page">
<header>
    <div style="width:90%;float:right">
    <h1><?php if($user) echo 'Welcome '.$user['mail'].', '; ?>OAuth 2.0 Test</h1>
    <nav>
      <ul>
        <li><a href="/client/index">Client</a></li>
        <li><a href="/server/index">Server</a></li>
        <li><a href="/api/index">API Resources</a></li>
        <li><a href="/oauth/error">Error Codes</a></li><?php
        if($user)
        {
            echo '<li><a href="/oauth/logout">Logout</a></li>';
        }
        else
        {
             echo '<li><a href="/oauth/signin">Sign In</a></li>';
        }
    ?></ul>
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
        <a href="#" class="fn url">OALite Inc.</a>
    </span><!-- /.primary -->
    <span class="bio">Open Application Lite is a web platform that offers online software serivce to personal and medium and small-sized enterprises.</span>
  </address><!-- /#about -->
</footer></div>
</body>
</html>
