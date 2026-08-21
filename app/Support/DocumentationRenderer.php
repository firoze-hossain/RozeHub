<?php

namespace App\Support;

class DocumentationRenderer
{
    public static function html(?string $markdown): string
    {
        $text = trim((string) $markdown);
        if ($text === '') return '';

        $lines = preg_split("/\r\n|\r|\n/", $text);
        $html = []; $inCode = false; $code = []; $inUl = false; $inOl = false; $inTable = false;

        $closeLists = function () use (&$html, &$inUl, &$inOl) {
            if ($inUl) { $html[] = '</ul>'; $inUl = false; }
            if ($inOl) { $html[] = '</ol>'; $inOl = false; }
        };

        foreach ($lines as $line) {
            if (preg_match('/^```(\w+)?\s*$/', trim($line), $m)) {
                if (!$inCode) { $closeLists(); $inCode = true; $code = []; }
                else { $lang = $m[1] ?? ''; $class = $lang ? ' class="language-'.e($lang).'"' : ''; $html[] = '<pre class="doc-code"><code'.$class.'>'.e(implode("\n", $code)).'</code></pre>'; $inCode = false; $code = []; }
                continue;
            }
            if ($inCode) { $code[] = $line; continue; }

            $trim = trim($line);
            if ($trim === '') { $closeLists(); if ($inTable) { $html[] = '</tbody></table>'; $inTable = false; } continue; }

            if (preg_match('/^\|(.+)\|$/', $trim)) {
                $cells = array_map(fn($v) => trim($v), explode('|', trim($trim, "|")));
                if (!$inTable) { $closeLists(); $html[] = '<table class="doc-table"><thead><tr>'.implode('', array_map(fn($c) => '<th>'.self::inline($c).'</th>', $cells)).'</tr></thead><tbody>'; $inTable = true; }
                elseif (!preg_match('/^:?-{3,}:?$/', $cells[0] ?? '')) { $html[] = '<tr>'.implode('', array_map(fn($c) => '<td>'.self::inline($c).'</td>', $cells)).'</tr>'; }
                continue;
            }
            if ($inTable) { $html[] = '</tbody></table>'; $inTable = false; }

            if (preg_match('/^(#{1,4})\s+(.+)$/', $trim, $m)) { $closeLists(); $level = strlen($m[1]) + 1; $slug = str($m[2])->slug(); $html[] = "<h{$level} id=\"{$slug}\">".self::inline($m[2])."</h{$level}>"; continue; }
            if (preg_match('/^[-*]\s+(.+)$/', $trim, $m)) { if (!$inUl) { $closeLists(); $html[] = '<ul>'; $inUl = true; } $html[] = '<li>'.self::inline($m[1]).'</li>'; continue; }
            if (preg_match('/^\d+\.\s+(.+)$/', $trim, $m)) { if (!$inOl) { $closeLists(); $html[] = '<ol>'; $inOl = true; } $html[] = '<li>'.self::inline($m[1]).'</li>'; continue; }
            if (str_starts_with($trim, '> ')) { $closeLists(); $html[] = '<blockquote>'.self::inline(substr($trim,2)).'</blockquote>'; continue; }
            $closeLists(); $html[] = '<p>'.self::inline($trim).'</p>';
        }
        if ($inCode) { $html[] = '<pre class="doc-code"><code>'.e(implode("\n", $code)).'</code></pre>'; }
        $closeLists(); if ($inTable) $html[] = '</tbody></table>';
        return implode("\n", $html);
    }

    private static function inline(string $value): string
    {
        $fontTokens = [];
        $value = preg_replace_callback('/\[\[font=([^;\]]*);color=(#[0-9a-fA-F]{6})?\]\]([\s\S]*?)\[\[\/font\]\]/', function ($m) use (&$fontTokens) {
            $face = preg_replace('/[^a-zA-Z0-9 ,\-]/', '', trim($m[1] ?? ''));
            $color = preg_match('/^#[0-9a-fA-F]{6}$/', $m[2] ?? '') ? $m[2] : '';
            $token = '__ROZE_FONT_' . count($fontTokens) . '__';
            $fontTokens[$token] = [$face, $color, $m[3] ?? ''];
            return $token;
        }, $value);
        $value = e($value);
        $value = preg_replace('/`([^`]+)`/', '<code>$1</code>', $value);
        $value = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $value);
        $value = preg_replace('/__([^_]+)__/', '<strong>$1</strong>', $value);
        $value = preg_replace('/~~([^~]+)~~/', '<s>$1</s>', $value);
        $value = preg_replace('/\+\+([^+]+)\+\+/', '<u>$1</u>', $value);
        $value = preg_replace('/==([^=]+)==/', '<mark>$1</mark>', $value);
        $value = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $value);
        $value = preg_replace_callback('/\[([^]]+)\]\((https?:\/\/[^\s)]+)\)/', fn($m) => '<a href="'.e($m[2]).'" target="_blank" rel="noopener">'.e($m[1]).'</a>', $value);
        foreach ($fontTokens as $token => [$face, $color, $content]) {
            $styles = [];
            if ($face !== '') $styles[] = 'font-family:' . $face . ';';
            if ($color !== '') $styles[] = 'color:' . $color . ';';
            $inner = self::inline($content);
            $value = str_replace(e($token), '<span class="doc-font-style" style="'.e(implode('', $styles)).'">'.$inner.'</span>', $value);
        }
        return $value;
    }
}
