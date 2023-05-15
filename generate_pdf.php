<?php
include_once("Parsedown.php");
include_once("helpers.php");

include_once("config.php");

$content = (new ContentGenerator($BASE_URL, $KEY, $PWD))->generateBodyAndSummary();
$body = $content["body"];
$summary = $content["summary"];

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $LANG; ?>" xml:lang="<?php echo $LANG; ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title><?php echo $TITLE ?></title>

    <link href="pdf_style.css" rel="stylesheet" type="text/css">

    <link href="./paged_interface.css" rel="stylesheet" type="text/css" />
    <script src="./paged.polyfill.js"></script>
</head>

<body>
    <header id="title-block-header">
        <h1 id="title"><?php echo $TITLE ?></h1>
        <p class="subtitle"><?php echo $SUBTITLE ?></p>
        <p id="version_date"><?php echo (new DateTime())->format("d/n/y"); ?></p>
    </header>

    <nav id="summary" role="doc-toc">
        <h2 id="title-summary"><?php echo $SUMMARY_TITLE ?></h2>
        <ul>
            <?php
            foreach ($summary as $i) {
                echo '<li><a href="#' . $i[0] . '">' . $i[1] . '</a></li>';
            }
            ?>
        </ul>
    </nav>

    <div class="content">
        <?php echo $body ?>
    </div>
</body>

</html>