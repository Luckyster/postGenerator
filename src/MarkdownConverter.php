<?php
namespace PostGenerator;

use Parsedown;

class MarkdownConverter {
    public static function toHtml($markdownText) {
        $parsedown = new Parsedown();
        return $parsedown->text($markdownText);
    }
}
