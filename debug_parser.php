<?php
$html = file_get_contents(__DIR__ . '/output2.html');
if (preg_match('/<title>(.*?)<\/title>/', $html, $matches)) {
    echo "TITLE: " . $matches[1] . "\n\n";
}
if (preg_match_all('/<div class="message">(.*?)<\/div>/s', $html, $matches)) {
    foreach ($matches[1] as $msg) {
        echo "MESSAGE: " . trim(strip_tags($msg)) . "\n";
    }
}
if (preg_match('/<div class="source.*?>(.*?)<\/div>/s', $html, $matches)) {
    echo "SOURCE: " . trim(strip_tags($matches[1])) . "\n";
}
