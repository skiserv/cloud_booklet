<?php

class Text
{
    public string $id;
    public function __construct(
        public string $title,
        public string $content,
        public array $tags = [],
        public ?string $lang = "",
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
        if ($strTags = self::extractMetadata('tags', $file)) {
            $tags = explode(" ", $strTags);
        }

        $lang = self::extractMetadata('lang', $file);

        return (new Text(
            title: $title,
            content: $file,
            tags: $tags,
            lang: $lang,
        ));
    }

    /**
     * Extract the metadata if exists and remove it from file content
     *
     * @param string $name
     * @param string $file
     * @return string|null
     */
    public static function extractMetadata(string $name, string &$file): ?string
    {
        $result = null;
        $regex = "/(" . $name . ":).*(\n|\Z)/";
        if (preg_match(
            $regex,
            $file,
            $matches,
        )) {
            $result = str_replace("\n", '', substr($matches[0], strlen($name) + 2));
            $file = preg_replace($regex, "", $file);
        }
        return $result;
    }
}
