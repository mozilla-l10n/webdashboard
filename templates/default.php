<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mozilla Web localization Dashboard</title>
    <link href="./assets/css/webdashboard.css" rel="stylesheet">
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
            <a id="tabzilla" href="https://www.mozilla.org">Mozilla</a>
            <header id="masthead">
                <h2><a href="./"><img alt="mozilla" src="./assets/images/sandstone/header-mozilla-stone.png"> Web Localization Dashboard</a></h2>
            </header>
            <article id="main-content">
        <?=$content?>
            </article>
        </div>

        <footer id="colophon">
          <div class="row">
              <div class="footer-logo">
                  <a href="http://mozilla.org"><img src="./assets/images/sandstone/footer-mozilla.png" alt="mozilla"></a>
              </div>

              <div class="footer-license">
                  <p>Portions of this content are ©1998–<?php echo date("Y"); ?> by individual mozilla.org contributors. Content available under a <a href="https://www.mozilla.org/foundation/licensing/website-content/">Creative Commons license</a>.</p>
              </div>

              <ul class="footer-nav">
                <li><a href="https://www.mozilla.org/about/privacy/">Privacy</a></li>
                <li class="wrap"><a href="https://www.mozilla.org/about/legal/">Legal</a></li>
              </ul>
          </div>
         </footer>
    </div>
</body>
</html>
