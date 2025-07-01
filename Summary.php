<?php

class Summary
{
    private array $texts = [];
    private array $textsByTags = [];

    public function __construct() {}

    public function registerText(Text $text)
    {
        $this->texts[$text->id] = $text->title;

        foreach ($text->tags as $tag) {
            if (!array_key_exists($tag, $this->textsByTags)) {
                $this->textsByTags[$tag] = [];
            }
            $this->textsByTags[$tag][$text->id] = $text->title;
        }
    }

    public function getSorted(?string $tag = null): array
    {
        $texts = $tag ? $this->textsByTags[$tag] : $this->texts;
        ksort($texts);
        return $texts;
    }

    public function getByFirstLetters(?string $tag = null): array
    {
        $byLetters = [];
        foreach ($this->getSorted($tag) as $id => $text) {
            $letter = strtoupper(substr($id, 0, 1));
            if ($letter == "À") {
                $letter = "A";
            }
            if (!array_key_exists($letter, $byLetters)) {
                $byLetters[$letter] = [];
            }

            $byLetters[$letter][$id] = $text;
        }
        return $byLetters;
    }

    public function getTags(): array
    {
        return array_keys($this->textsByTags);
    }
}
