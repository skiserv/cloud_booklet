<?php

class Summary
{
    private array $texts = [];

    public function __construct() {}

    public function registerText(Text $text)
    {
        $this->texts[$text->id] = $text->title;
    }

    public function getSorted(): array
    {
        ksort($this->texts);
        return $this->texts;
    }

    public function getByFirstLetters(): array
    {
        $byLetters = [];
        foreach ($this->getSorted() as $id => $text) {
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
}
