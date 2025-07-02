<?php

class Text
{
    public string $id;
    public function __construct(
        public string $title,
        public string $content,
        public array $tags = [],
    ) {
        $this->id = str_replace(
            [" ", "'", "À", "à"],
            ["-", "-", "a", "a"],
            strtolower($this->title),
        );
    }

    public function getContentAsHTML(): string
    {
        return (new Parsedown())->text($this->content);
    }
}

class TextFactory
{
    public static function createFromMarkdownFile(string $file): Text
    {
        /* Get first high level title as main title, or by default first words */
        $title = null;
        while ($line = strtok($file, "\n\r")) {
            if (str_starts_with($line, "# ")) {
                $title = substr($line, 2);
                break;
            }
        }
        if (!$title) {
            $title = substr($file, 0, 10);
        }

        /* Extracts tags if exists and remove it from content */
        $tags = [];
        $tagsRegex = "/(tags:).*(\n|\Z)/";
        if (preg_match(
            $tagsRegex,
            $file,
            $tagsAsString,
        )) {
            $tags = explode(" ", substr($tagsAsString[0], 6));
            $file = preg_replace($tagsRegex, "", $file);
        }

        return (new Text(
            title: $title,
            content: $file,
            tags: $tags,
        ));
    }
}
