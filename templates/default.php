<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mozilla Web localization Dashboard</title>
    <link href="./assets/css/sandstone.css"  rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href="//www.mozilla.org/tabzilla/media/css/tabzilla.css" rel="stylesheet">
    <link rel="icon" href="./assets/images/mozilla-l10n.png" type="image/png">

    <style>
        #wrapper {
            min-height: 800px;
        }

        #masthead h2 {
            padding-top: 90px
        }

        h2 small {
            font-size:60%; font-style:italic;
        }

        div#locales {
            -moz-column-count: 5;
            width: 100%;
        }

        #main-content table {
            text-align:center;
            border-collapse: collapse;
            margin-bottom: 1em;
            background-color: rgba(255,255,255,0.4);
            min-width: 600px;
        }

        #main-content table th {
            text-align:left;
        }

        #main-content table th.col2 {
            text-align:center;
        }

        #main-content table .col1 {
            width: 400px;
        }

        #main-content table td,
        #main-content table th {
            border:1px solid lightgray;
            padding: 1px 10px;
        }

        .feed {
            background-color: rgba(255,255,255,0.4);
            height: 48px;
            width:600px;
            border-radius:5px;
            line-height:48px
        }

        .feed:hover {
            background-color: rgba(255,255,255,0.8);
        }

        .feed a {
            display:block;
            width:100%;
            height:100%;
        }

        .feed a img{
            float: left;
            margin-right: 5px;
        }

        #locale {
            font-size: 80px;
            float:right;
            font-weight: bold;
            border:1px solid lightgray;
            height:92px;
            line-height:88px;
            min-width:150px;
            padding:4px;
            text-align:center;
            margin-top: -50px;
            background: rgba(255,255,255, 0.4);
        }

        .bugbox1 {
            background: rgba(255,255,255, 0.4);
            margin-bottom:1em
        }

        .bugbox1 .bugdesc {
            font-weight:bold;
        }

        .hideme {
            display:none;
        }

    </style>

</head>
<body class="sand">
<div id="outer-wrapper">
    <div id="wrapper">

    <header id="masthead">
        <a id="tabzilla" href="//www.mozilla.org/" class="tabzilla-closed">Mozilla</a>

        <nav role="navigation" id="nav-main">
        <ol>
            <li><a href="//www.mozilla.org/mission/">Mission</a></li>
            <li><a href="//www.mozilla.org/about/">About</a></li>
            <li><a href="//www.mozilla.org/projects/">Projects</a></li>
            <li><a href="//www.mozilla.org/contribute/">Get Involved</a></li>
        </ol>
        </nav>

    <h2><a href="./"><img alt="mozilla" src="./assets/images/sandstone_theme/header-mozilla-stone.png"> Web Localization Dashboard</a></h2>


    </header>
        <article id="main-content">
<?=$content?>
      </article>

    </div>
    <footer id="colophon">
      <div class="row">
          <div class="footer-logo">
              <a href="http://mozilla.org"><img src="./assets/images/sandstone_theme/footer-mozilla.png" alt="mozilla"></a>
          </div>

          <div class="footer-license">
              <p>Except where otherwise <a href="http://www.mozilla.org/en-US/about/legal.html#site">noted</a>, content on this site is licensed under the <a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution Share-Alike License v3.0</a> or any later version.</p>
          </div>

          <ul class="footer-nav">
            <li><a href="/privacy/">Privacy Policy</a></li>
            <li><a href="http://mozilla.org/about/legal.html">Legal Notices</a></li>
          </ul>
      </div>
</body>
</html>