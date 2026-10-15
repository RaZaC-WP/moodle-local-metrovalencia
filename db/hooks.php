<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core_user\hook\extend_user_menu::class,
        'callback' => [
            \local_metrovalencia\hook_callbacks::class,
            'extend_user_menu',
        ],
    ],
    [
        'hook' => \core\hook\navigation\primary_extend::class,
        'callback' => [
            \local_metrovalencia\hook_callbacks::class,
            'extend_primary_navigation',
        ],
    ],
];