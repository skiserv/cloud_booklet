<?php

class SinglePage
{
    private string $body = "";

    public function __construct(private Config $conf) {}

    public function getHTML(): string
    {
        return '<!DOCTYPE html>
<html lang="' . $this->conf::LANG . '">
<head>' . $this->getHead() . '
</head>
<body>
' . $this->getHeader() . '
' . $this->body . '
</body>
</html>';
    }

    private function getHead(): string
    {
        return '
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>' . $this->conf::TITLE . '</title>
    
    <link rel="manifest" href="manifest.json">
    
    <link rel="stylesheet" href="website_style.css">
    <link rel="stylesheet" href="custom/style.css">

    <link rel="icon" type="image/x-icon" href="/custom/' . $this->conf::LOGO_FILE . '">';
    }

    private function getHeader(): string
    {
        $html = '   <header>
        <h1>
            <a href="#home">' . $this->conf::TITLE . '</a>
        </h1>
        <nav>
            <a href="#home">' . $this->conf::SUMMARY_TITLE . '</a>';

        foreach ($this->conf::PAGES as $text => $id) {
            $html = $html . '
            <a href="#' . explode(".", $id)[0] . '">' . $text . '</a>';
        }

        $html = $html . '
        </nav>
    </header>';
        return $html;
    }

    public function getSummary(Summary $summary, ?string $tag = null): string
    {
        $html = "";
        if ($tag) {
            $html = $html . '<h2>' . $tag . '</h2>';
        }

        $html = $html . $this->getTags($summary->getTags(), withHome: true);

        foreach ($summary->getByFirstLetters($tag) as $letter => $letterSummary) {
            $html = $html . '<h3>' . $letter . '</h3><ul>';
            foreach ($letterSummary as $id => $title) {
                $html = $html . '
            <li><a href="#section-' . $id . '">' . $title . '</a></li>';
            }
            $html = $html . '</ul>';
        }

        return $html;
    }

    public function getTags(array $tags, bool $withHome = false): string
    {
        $html = '<div id="tags">';
        if ($withHome) {
            $html = $html . '
                <a href="#home">*</a>';
        }
        foreach ($tags as $tag) {
            $html = $html . '
            <a href="#tag-' . $tag . '">' . $tag . '</a>';
        }
        $html = $html . '
        </div>';
        return $html;
    }

    public function addSection(string $id, string $content): void
    {
        $this->body = $this->body . '<section id="' . $id . '">' . $content . '</section>';
    }
}
