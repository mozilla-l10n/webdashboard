<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mozilla Web localization Dashboard</title>
    <link href="./assets/css/sandstone.css" rel="stylesheet">
    <link href="./assets/css/webdashboard.css" rel="stylesheet">
    <link href="//mozorg.cdn.mozilla.net/media/css/tabzilla-min.css" rel="stylesheet">
    <link rel="icon" href="./assets/images/mozilla-l10n.png" type="image/png">
    <?php
        if (isset($links)) {
            echo $links;
        }
    ?>
</head>
<body class="<?=$body_class?>">
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
         </footer>
    </div>
<script src="//mozorg.cdn.mozilla.net/en-US/tabzilla/tabzilla.js"></script>
</body>
</html>