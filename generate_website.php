<?php
include_once("src/Parsedown.php");
include_once("src/helpers.php");
include_once("src/Text.php");
include_once("src/Summary.php");
include_once("src/SinglePage.php");

include_once("config.php");

$conf = new Config();

$contentGenerator = new ContentGenerator($conf::BASE_URL, $conf::KEY, $conf::PWD);
$content = $contentGenerator->generateBodyAndSummary();
$texts = $content["texts"];
$summary = $content["summary"];


$page = new SinglePage($conf);

$page->addSection("home", $page->getSummary($summary));

foreach ($summary->getTags() as $tag) {
    $page->addSection("tag-$tag", $page->getSummary($summary, $tag));
}

foreach ($contentGenerator->getSpecials($conf::PAGES) as $id => $content) {
    $page->addSection(explode(".", $id)[0], $content);
}

foreach ($texts as $text) {
    $page->addSection(
        'section-' . $text->id,
        $text->getContentAsHTML() . $page->getTags($text->tags)
    );
}

$file = fopen("index.html", "w");
fwrite($file, $page->getHTML());
fclose($file);

file_put_contents(
    "manifest.json",
    '{
        "name": "' . $conf::TITLE . '",
        "short_name": "' . $conf::TITLE . '",
        "start_url": "./index.html",
        "scope": "./",
        "icons": [
            {
                "src": "/custom/' . $conf::LOGO_FILE . '",
                "sizes": "' . $conf::LOGO_SIZE . '",
                "type": "' . $conf::LOGO_TYPE . '"
            }
        ],
        "theme_color": "' . $conf::PWA_THEME_COLOR . '",
        "background_color": "' . $conf::PWA_BACKGROUND_COLOR . '",
        "display": "standalone"
    }',
);

echo "Generation succeed ! 🥳 Go back to <a href=\"index.html\">website</a> now\n";
