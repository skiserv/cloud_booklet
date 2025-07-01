<?php
include_once("Parsedown.php");
include_once("helpers.php");
include_once("Text.php");
include_once("Summary.php");

include_once("config.php");

echo "+++ Website generation started \n";
echo "+++ Fetching the cloud content, this may take some time. \n";

$contentGenerator = new ContentGenerator($BASE_URL, $KEY, $PWD);
$content = $contentGenerator->generateBodyAndSummary();
$texts = $content["texts"];
$summary = $content["summary"];

echo "+++ Content fetched \n";

function writeSummary(Summary $summary, $file, ?string $tag = null)
{
    if ($tag) {
        fwrite($file, '<h2>' . $tag . '</h2>');
    }

    fwrite($file, '<div id="tags">');

    fwrite($file, '
            <a href="#home">*</a>');
    foreach ($summary->getTags() as $existingTag) {
        fwrite($file, '
            <a href="#tag-' . $existingTag . '">' . $existingTag . '</a>');
    }
    fwrite($file, '
        </div>');

    foreach ($summary->getByFirstLetters($tag) as $letter => $letterSummary) {
        fwrite($file, '<h3>' . $letter . '</h3>
    <ul>');
        foreach ($letterSummary as $id => $title) {
            fwrite($file, '
            <li><a href="#section-' . $id . '">' . $title . '</a></li>');
        }
        fwrite($file, '</ul>');
    }
}


$file = fopen("index.html", "w");
fwrite($file, '
<!DOCTYPE html>
<html lang="' . $LANG . '">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>' . $TITLE . '</title>
    
    <link rel="manifest" href="manifest.json">
    
    <link rel="stylesheet" href="website_style.css">
    <link rel="stylesheet" href="custom/style.css">

    <link rel="icon" type="image/x-icon" href="/custom/' . $LOGO_FILE . '">
</head>

<body>
    <header>
        <h1>
            <a href="#home">' . $TITLE . '</a>
        </h1>
        <nav>
        <a href="#home">' . $SUMMARY_TITLE . '</a>');

foreach ($PAGES as $text => $id) {
    fwrite($file, '
        <a href="#' . explode(".", $id)[0] . '">' . $text . '</a>');
}


fwrite($file, '
        </nav>
    </header>

    <section id="home">');

writeSummary($summary, $file);

fwrite($file, '
    </section>');

foreach ($summary->getTags() as $tag) {
    fwrite($file, '
    <section id="tag-' . $tag . '">');
    writeSummary($summary, $file, $tag);
    fwrite($file, '
    </section>');
}


foreach ($contentGenerator->getSpecials($PAGES) as $id => $content) {
    fwrite($file, '
            <section id="' . explode(".", $id)[0] . '">
            ' . $content . '
            </section>
        ');
}

foreach ($texts as $text) {
    fwrite($file, '
    <section id="section-' . $text->id . '">' . $text->getContentAsHTML());

    fwrite($file, '<div id="tags">');
    foreach ($text->tags as $tag) {
        fwrite($file, '
            <a href="#tag-' . $tag . '">' . $tag . '</a>');
    }
    fwrite($file, '</div>
    </section>');
}

fwrite($file, '
</body>

</html>');

fclose($file);

echo "+++ `index.html` created \n";

file_put_contents(
    "manifest.json",
    '{
        "name": "' . $TITLE . '",
        "short_name": "' . $TITLE . '",
        "start_url": "./index.html",
        "scope": "./",
        "icons": [
            {
                "src": "/custom/' . $LOGO_FILE . '",
                "sizes": "' . $LOGO_SIZE . '",
                "type": "' . $LOGO_TYPE . '"
            }
        ],
        "theme_color": "' . $PWA_THEME_COLOR . '",
        "background_color": "' . $PWA_BACKGROUND_COLOR . '",
        "display": "standalone"
    }',
);
echo "+++ `manifest.json created \n";
echo "+++ Website generated with success \n";
