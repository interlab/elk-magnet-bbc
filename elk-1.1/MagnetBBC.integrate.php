<?php

if (!defined('ELK'))
    die('No access...');

class MagnetBBC
{
    static public function integrate_load_theme()
    {
        loadCSSFile('magnet-bbcode.css');
    }

    // hook integrate_additional_bbc /sources/subs/BBC/ParserWrapper.php
    static public function integrate_additional_bbc(&$additional_bbc)
    {
        $ary = [[
                // [tag]unparsed content[/tag]
                BBC\Codes::ATTR_TAG => 'magnet',
                BBC\Codes::ATTR_TYPE => BBC\Codes::TYPE_UNPARSED_CONTENT,
                BBC\Codes::ATTR_CONTENT => '<img src="__magnet_ico__" />&nbsp;<a href="$1" class="new_win">__magnet_name__</a>',
                BBC\Codes::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
                    global $settings;
                    if (!preg_match('~^[a-z0-9]{40}$~i', $data)) {
                        $tag[BBC\Codes::ATTR_CONTENT] = '[magnet]$1[/magnet]';
                        return;
                    }
                    $data = strtr($data, ['<br />' => '']);
                    // $magnet_ico = $settings['images_url'] . '/bbc/' . 'magnet.png';
                    $magnet_ico = $settings['default_images_url'] . '/bbc/' . 'magnet.png';
                    $magnet_name = $data;
                    $ml = 'magnet:?xt=urn:btih:';
                    // if (strpos($data, $ml) !== 0) {
                    $data = $ml . $data;
                    // }
                    $tag[BBC\Codes::ATTR_CONTENT] = strtr($tag[BBC\Codes::ATTR_CONTENT], [
                        '__magnet_ico__' => $magnet_ico,
                        '__magnet_name__' => $magnet_name,
                    ]);
                },
                BBC\Codes::ATTR_BLOCK_LEVEL => false,
                BBC\Codes::ATTR_AUTOLINK => false,
                BBC\Codes::ATTR_LENGTH => 6,
                BBC\Codes::ATTR_DISABLED_CONTENT => '[magnet]$1[/magnet]',
            ],
            [
                // [tag=xyz]parsed content[/tag]
                BBC\Codes::ATTR_TAG => 'magnet',
                BBC\Codes::ATTR_TEST => '[A-Za-z0-9]{40}',
                BBC\Codes::ATTR_TYPE => BBC\Codes::TYPE_UNPARSED_EQUALS,
                BBC\Codes::ATTR_BEFORE => '<img src="__magnet_ico__" />&nbsp;<a href="$1" class="new_win">',
                BBC\Codes::ATTR_AFTER => '</a>',
                BBC\Codes::ATTR_VALIDATE => function(&$tag, &$data, $disabled) {
                    global $settings;
                    if (!empty($disabled['magnet'])) {
                        $tag[BBC\Codes::ATTR_BEFORE] = '[magnet=$1]';
                        $tag[BBC\Codes::ATTR_CONTENT] = '$2';
                        $tag[BBC\Codes::ATTR_AFTER] = '[/magnet]';
                        return;
                    }
                    $data = strtr($data, ['<br />' => '']);
                    // $icon = $settings['images_url'] . '/bbc/' . 'magnet.png';
                    $icon = $settings['default_images_url'] . '/bbc/' . 'magnet.png';
                    $ml = 'magnet:?xt=urn:btih:';
                    // if (strpos($data, $ml) !== 0) {
                    $data = $ml . $data;
                    // }
                    $tag[BBC\Codes::ATTR_BEFORE] = strtr($tag[BBC\Codes::ATTR_BEFORE], [
                        '__magnet_ico__' => $icon,
                    ]);
                },
                BBC\Codes::ATTR_BLOCK_LEVEL => false,
                BBC\Codes::ATTR_AUTOLINK => false,
                BBC\Codes::ATTR_LENGTH => 6,
                BBC\Codes::ATTR_DISALLOW_CHILDREN => ['email', 'ftp', 'url', 'iurl'],
                BBC\Codes::ATTR_DISABLED_BEFORE => '[magnet=$1]',
                BBC\Codes::ATTR_DISABLED_CONTENT => '$2',
                BBC\Codes::ATTR_DISABLED_AFTER => '[/magnet]',
        ]];
        $additional_bbc = array_merge($additional_bbc, $ary);
    }

    // Editor.subs.php
    static public function integrate_bbc_buttons(&$bbc_tags)
    {
        global $context;

        $where = $bbc_tags['row2'][3];
        // And here we insert the new value after code
        $bbc_tags['row2'][3] = elk_array_insert($where, 'link', ['magnet'], 'after', false);

        loadJavascriptFile('magnet-bbcode.js');
    }
}
