<?php

namespace App\Http\Controllers;

use Parsedown;

class DocumentationController extends Controller
{
    const CSS_PATH = '';
    const DOC_PATH = 'docs';

    public function show($name = 'index')
    {
        $name = strtok($name, '.');
        $name = preg_replace('@^(docs/)*@', '', $name);
        if ($name === 'index') {
            $file_path = base_path('README.md');
        } else {
            $file_path = base_path(static::DOC_PATH . DIRECTORY_SEPARATOR . $name . '.md');
        }
        if (!file_exists($file_path)) {
            $file_path = dirname($file_path) . DIRECTORY_SEPARATOR . 'README.md';
        }
        if (!file_exists($file_path)) {
            abort(404);
        }
        $content = static::parsedown($file_path);
        if (1 === preg_match('#<h1>(.*?)</h1>#su', $content, $matches)) {
            $title = strip_tags($matches[1]);
        } else {
            $title = '';
        }
        return view('documentation', compact('title','content'));
    }

    public static function parsedown($file_path)
    {
        $markdown = preg_replace('/(\\[.*?\\]\\(.*?)\\.md\\)/', '$1.html)', file_get_contents($file_path));
        $Parsedown = new Parsedown();
        return $Parsedown->text($markdown);
    }
}
