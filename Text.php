<?php

class Text
{
    public string $id;
    public function __construct(
        public string $title,
        public string $content,
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
    public static function createFromMarkdownFile(string $file)
    {
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

        return (new Text(
            title: $title,
            content: $file
        ));
    }
}
