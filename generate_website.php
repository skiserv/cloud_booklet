<?php
include_once("Parsedown.php");
include_once("helpers.php");

include_once("config.php");

echo '+++ Website generation started <br/>';
echo '+++ Fetching the cloud content, this may take some time. <br/>';

$contentGenerator = new ContentGenerator($BASE_URL, $KEY, $PWD);
$content = $contentGenerator->generateBodyAndSummary();
$body = $content["body"];
$summary = $content["summary"];

echo '+++ Content fetched <br/>';

$index_file = fopen("index.html", "w");
fwrite($index_file, '
<!DOCTYPE html>
<html lang="' . $LANG . '">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>' . $TITLE . '</title>
    
    <link rel="manifest" href="manifest.json">
    
    <link rel="stylesheet" href="website_style.css">
    <link rel="stylesheet" href="custom/style.css">

    <link rel="icon" type="image/x-icon" href="/' . $LOGO_FILE . '">
</head>

<body>
    <header>
        <h1>
            <a href="#home">' . $TITLE . '</a>
        </h1>
        <nav>
        <a href="#home">' . $SUMMARY_TITLE . '</a>');

foreach ($PAGES as $text => $id) {
    fwrite($index_file, '
        <a href="#' . explode(".", $id)[0] . '">' . $text . '</a>');
}


fwrite($index_file, '
        </nav>
    </header>

    <section id="home">
        <ul>');
foreach ($summary as $i) {
    fwrite($index_file, '
            <li><a href="#section-' . $i[0] . '">' . $i[1] . '</a></li>');
}
fwrite($index_file, '
        </ul>
    </section>');

foreach ($contentGenerator->getSpecials($PAGES) as $id => $content) {
    fwrite($index_file, '
            <section id="' . explode(".", $id)[0] . '">
            ' . $content . '
            </section>
        ');
}

fwrite($index_file, '
    ' . $body . '
</body>

</html>');

fclose($index_file);

echo "+++ `index.html` created <br/>";

file_put_contents(
    "manifest.json",
    '{
        "name": "' . $TITLE . '",
        "short_name": "' . $TITLE . '",
        "start_url": "./index.html",
        "scope": "./",
        "icons": [
            {
                "src": "' . $LOGO_FILE . '",
                "sizes": "' . $LOGO_SIZE . '",
                "type": "' . $LOGO_TYPE . '"
            }
        ],
        "theme_color": "' . $PWA_THEME_COLOR . '",
        "background_color": "' . $PWA_BACKGROUND_COLOR . '",
        "display": "standalone"
    }',
);
echo "+++ `manifest.json created <br/>";
echo '+++ Website generated with success <br/>';
