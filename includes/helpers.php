<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('edu_get_field')) {
function edu_get_field($field_name, $post_id = false) {
if (!function_exists('get_field')) {
return '';
}

return get_field($field_name, $post_id);
}
}

if (!function_exists('edu_get_sub_field')) {
function edu_get_sub_field($field_name) {
if (!function_exists('get_sub_field')) {
return '';
}

return get_sub_field($field_name);
}
}

if (!function_exists('edu_get_post_types')) {
function edu_get_post_types() {
return ['prof-perepodgotovka', 'povyshenie-kvalifika', 'professionalnoe-obuc', 'self_propelled_machi', 'labor-protection'];
}
}

if (!function_exists('edu_get_sections_map')) {
function edu_get_sections_map() {
return [
'prof-perepodgotovka' => [
'label'     => 'Профессиональная переподготовка',
'post_type' => 'prof-perepodgotovka',
'page_id'   => 103,
],
'povyshenie-kvalifikacii' => [
'label'     => 'Повышение квалификации',
'post_type' => 'povyshenie-kvalifika',
'page_id'   => 109,
],
'professionalnoe-obuchenie' => [
'label'     => 'Профессиональное обучение',
'post_type' => 'professionalnoe-obuc',
'page_id'   => 105,
],
'self-propelled-machines' => [
'label'     => 'Самоходные машины (трактористы-машинисты)',
'post_type' => 'self_propelled_machi',
'page_id'   => 30288,
],
'labor-protection' => [
'label'     => 'Охрана труда',
'post_type' => 'labor-protection',
'page_id'   => 30348,
],
];
}
}

if (!function_exists('edu_get_section_by_page_id')) {
function edu_get_section_by_page_id($page_id) {
foreach (edu_get_sections_map() as $slug => $data) {
if ((int) $data['page_id'] === (int) $page_id) {
return $slug;
}
}

return '';
}
}

if (!function_exists('edu_get_section_by_page_title')) {
function edu_get_section_by_page_title($title) {
$normalized_title = mb_strtolower(trim((string) $title));

foreach (edu_get_sections_map() as $slug => $data) {
$label = mb_strtolower(trim((string) $data['label']));

if ($normalized_title === $label) {
return $slug;
}

if ($slug === 'self-propelled-machines' && mb_strpos($normalized_title, 'самоходные машины') !== false) {
return $slug;
}
}

return '';
}
}

if (!function_exists('edu_get_section_url')) {
function edu_get_section_url($slug, $data) {
if (!empty($data['page_id'])) {
$url = get_permalink((int) $data['page_id']);
if ($url) {
return $url;
}
}

$page = get_page_by_path($slug);
if ($page) {
return get_permalink($page->ID);
}

if (!empty($data['label'])) {
$page = get_page_by_path(sanitize_title($data['label']));
if ($page) {
return get_permalink($page->ID);
}
}

return '#';
}
}

if (!function_exists('edu_get_section_by_post_type')) {
function edu_get_section_by_post_type($post_type) {
foreach (edu_get_sections_map() as $slug => $data) {
if ($data['post_type'] === $post_type) {
return $slug;
}
}

return '';
}
}

if (!function_exists('edu_get_section_label_by_post_type')) {
function edu_get_section_label_by_post_type($post_type) {
$slug = edu_get_section_by_post_type($post_type);
$sections = edu_get_sections_map();

return $slug && isset($sections[$slug]['label']) ? $sections[$slug]['label'] : '';
}
}


if (!function_exists('edu_get_section_url_by_post_type')) {
function edu_get_section_url_by_post_type($post_type) {
$slug = edu_get_section_by_post_type($post_type);
$sections = edu_get_sections_map();

if ($slug && isset($sections[$slug])) {
return edu_get_section_url($slug, $sections[$slug]);
}

return '#';
}
}
