<?php

namespace jdavidbakr\MailTracker\Libraries;

class TinyMinify {
    static function html($html, $options = []) {
        $minifier = new TinyHtmlMinifier($options);
        return $minifier->minify($html);
    }
}
