<?php
/**
 * php script to generate /items/static.html from /api/items.json
 * @author remi@e-responsable.com
 */

// my source of data.json
$data = json_decode(file_get_contents(dirname(__FILE__) . '/items.json'), true);
// public static cache dir
$publicDir = dirname(__FILE__) . '/../blog/';
// my html template : Todo : move on symfony for the next step
$tpl = file_get_contents(dirname(__FILE__) . '/../../tpl/index.twig');

// Pray the glory loop |o|
foreach ($data as $item) {
    // init new blank content var
    $itemHtmlContent = $tpl;
    $upsertType = 'create';
    if (file_exists( $publicDir . $item['slug'] . '.html')) {
        $upsertType = 'update';
    }

    // kinda basic Dto -> format transformation,
    // Todo: make a Php class to have autocomplete and fully controlled format
    $dynamicContents = [
        '{{ item.slug }}' => $item['slug'],
        '{{ item.title }}' => $item['title'],
        '{{ item.author }}' => $item['author'],
        // my data src have undefined `content` when it's not done yet
        '{{ item.content }}' => $item['content'] ?? '⚠️',
        '{{ item.publication_date }}' => $item['publication_date'], // todo: format date
        '{{ item.last_generation }}' => date('Y-m-d H:i:s'), // todo: format date
        // Todo: move on symfony dude ! at this point it's enough rebuild from scratch...
        '{{ item.sources }}' => implode('<br/>', $item['sources'] ?? []),
    ];
    // replace {{ var }} with dynamic content : this IS the artificial intelligence "generative" of this script ! 😏
    foreach ($dynamicContents as $name => $content) {
        $itemHtmlContent = str_replace($name, $content, $itemHtmlContent);
    }

    // save cache in public dir
    file_put_contents($publicDir . $item['slug'] . '.html',
        $itemHtmlContent
    );

    // TODO: update sitemap

    // Hello world & debug stack trace
    echo $upsertType . ' ' . $item['slug'] . chr(10);
}
