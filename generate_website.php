<?php
include_once("Parsedown.php");
include_once("helpers.php");

include_once("config.php");

echo '+++ Website generation started <br/>';
echo '+++ Fetching the cloud content, this may take some time. <br/>';


$content = (new ContentGenerator($BASE_URL, $KEY, $PWD))->generateBodyAndSummary();
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
</head>

<body>
    <header>
        <h1>
            <a href="#home">' . $TITLE . '</a>
        </h1>
        <nav>
        <a href="#home">' . $SUMMARY_TITLE . '</a>
        <a href="#about">' . $ABOUT_TITLE . '</a>
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
    </section>

    <section id="about">
        <p>' . $SUBTITLE . '</p>
        <p>' . $ABOUT_CONTENT . '</p>
        <p>Website created : ' . (new DateTime())->format("d/n/y") . '</p>
    </section>

    ' . $body . '
    
    <script>
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker
                .register("/sw.js")
                .then(res => console.log("service worker registered"))
                .catch(err => console.log("service worker not registered", err))
        })
    }
    </script>
</body>

</html>');

fclose($index_file);

echo "+++ `index.html` created <br/>";

file_put_contents(
    "manifest.json",
    '{
        "name": ' . $TITLE . ',
        "short_name": "' . $TITLE . '",
        "start_url": "./index.html",
        "scope": "./",
        "icons": [
            {
                "src": "icon.svg",
                "sizes": "192x192",
                "type": "image/svg+xml"
            }
        ],
        "theme_color": "' . $PWA_THEME_COLOR . '",
        "background_color": "' . $PWA_BACKGROUND_COLOR . '",
        "display": "standalone"
    }',
);
echo "+++ `manifest.json created <br/>";
echo '+++ Website generated with success <br/>';
