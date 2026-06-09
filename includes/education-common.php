<?php
/**
* Education snippets: common helpers, styles, sidebar, cards and popup search.
* Shortcodes:
* [education_breadcrumbs]
* [education_sidebar]
* [direction_cards type="prof-perepodgotovka"]
* [direction_cards type="self_propelled_machi"]
* [education_popup_search]
*/

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

if (!function_exists('edu_render_education_breadcrumbs')) {
function edu_render_education_breadcrumbs($items = []) {
if (empty($items)) {
$items[] = [
'label' => 'Главная',
'url'   => home_url('/?project=dopolnitelnoe-obrazovanie'),
];

if (is_singular(edu_get_post_types())) {
$post_type = get_post_type(get_the_ID());
$section_label = edu_get_section_label_by_post_type($post_type);
$section_url = edu_get_section_url_by_post_type($post_type);

if ($section_label) {
$items[] = [
'label' => $section_label,
'url'   => $section_url,
];
}

$items[] = [
'label' => get_the_title(get_the_ID()),
'url'   => '',
];
} elseif (is_page()) {
$items[] = [
'label' => get_the_title(get_the_ID()),
'url'   => '',
];
} else {
$items[] = [
'label' => single_post_title('', false),
'url'   => '',
];
}
}

if (count($items) < 2) {
return '';
}

ob_start();
?>
<nav class="edu-breadcrumb" aria-label="Хлебные крошки">
  <?php foreach ($items as $index => $item) : ?>
  <?php if ($index > 0) : ?>
  <svg viewBox="0 0 6 10" class="edu-breadcrumb__separator" aria-hidden="true" focusable="false"><path d="M0 0 L5 5 L0 10" fill="none" stroke="currentColor" /></svg>
  <?php endif; ?>

  <?php if (!empty($item['url']) && $index < count($items) - 1) : ?>
  <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
  <?php else : ?>

  <span aria-current="page"><?php echo esc_html($item['label']); ?></span>
  <?php endif; ?>
  <?php endforeach; ?>
</nav>
<?php
return ob_get_clean();
}
}

add_action('wp_enqueue_scripts', function () {
wp_register_style('education-sidebar-inline', false);
wp_enqueue_style('education-sidebar-inline');

wp_enqueue_style(
'font-awesome',
'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
[],
'6.5.2'
);

$css = <<<'CSS'
.edu-layout {
display: grid;
grid-template-columns: minmax(0, 1fr) 290px ;
gap: 28px;
align-items: start;
}


.edu-breadcrumb {
display: flex;
align-items: center;
flex-wrap: wrap;
gap: 8px;
width: 100%;
margin: 0 auto 18px;
padding: 12px 20px;
background: #F5F7FB;
border-radius: 8px;
color: #1A1A1A;
font-family: 'Segoe UI', sans-serif;
font-size: 16px;
line-height: 1.35;
}

.edu-breadcrumb a {
color: #1A73E8;
text-decoration: none;
font-weight: 500;
transition: color 0.2s ease;
}

.edu-breadcrumb a:hover {
color: #0D47A1;
text-decoration: underline;
}

.edu-breadcrumb span {
font-weight: 600;
color: #333333;
white-space: nowrap; /* Запрещает перенос текста на новую строку */
overflow: hidden; /* Скрывает текст, который выходит за границы блока */
text-overflow: ellipsis; /* Добавляет многоточие (...) вместо обрезанного текста */
}

.edu-breadcrumb__separator {
width: 6px;
height: 10px;
color: #999999;
flex-shrink: 0;
}

.edu-sidebar {
padding: 14px;
border: 1px solid #F1F1F1;
background: #FFFFFF;
border-radius: 16px;
}

.edu-sidebar__group {
margin-bottom: 10px;
}

.edu-sidebar__group:last-child {
margin-bottom: 0;
}

.edu-sidebar__head {
display: flex;
align-items: center;
justify-content: space-between;
gap: 12px;
min-height: 42px;
padding: 15px;
background: #F5F7FA;
border-radius: 10px;
font-size: 15px;
font-weight: 500;
line-height: 1.2;
color: #3F3F46;
}

.edu-sidebar__title {
text-decoration: none;
color: inherit;
flex: 1;
}

.edu-sidebar__toggle {
background: transparent;
border: none;
cursor: pointer;
padding: 4px;
display: flex;
align-items: center;
}

.edu-sidebar__icon {
display: flex;
align-items: center;
justify-content: center;
transition: transform 0.2s ease;
color: #52525B;
font-size: 14px;
}

.edu-sidebar__group.is-open .edu-sidebar__icon {
transform: rotate(180deg);
}

.edu-sidebar__body {
display: none;
background: #FFFFFF;
padding-top: 10px;
}

.edu-sidebar__group.is-open .edu-sidebar__body {
display: block;
}

.edu-sidebar__list {
list-style: none !important;
margin: 0;
padding: 0;
}

.edu-sidebar__item {
list-style: none !important;
margin: 0;
padding: 12px 0;
border-bottom: 1px solid #F1F1F1;
}

.edu-sidebar__item:last-child {
border-bottom: none;
}

.edu-sidebar__item a {
display: -webkit-box;
-webkit-box-orient: vertical;
-webkit-line-clamp: 2;
max-height: calc(1.85em * 2);
padding: 12px 16px;
overflow: hidden;
text-decoration: none;
color: #5F5F5F;
font-size: 14px;
line-height: 1.35;
background: transparent;
}

.edu-sidebar__item a:hover,
.edu-sidebar__item.is-current a {
color: #2F2F2F;
}

.edu-sidebar__item.is-current a {
font-weight: 600;
}

.edu-sidebar__empty {
display: block;
padding: 12px 16px;
color: #6B7280;
font-size: 14px;
}

.edu-cards {
display: grid;
grid-template-columns: repeat(2, minmax(0, 1fr));
gap: 16px;
}

.edu-card {
background: #FFFFFF;
border: 1px solid #F1F1F1;
border-radius: 16px;
padding: 18px;
transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}

.edu-card:hover {
transform: translateY(-2px);
border-color: #D9DEE7;
box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06);
}

.edu-card__link {
display: block;
text-decoration: none;
color: inherit;
height: 100%;
}

.edu-card__badges {
display: flex;
flex-wrap: wrap;
gap: 10px;
margin-bottom: 14px;
}

.edu-card__badge {
display: inline-flex;
align-items: center;
gap: 7px;
min-height: 36px;
padding: 0 12px;
border: 1px solid #AEB4BF;
border-radius: 999px;
background: #FFFFFF;
color: #2F3440;
font-size: 14px;
line-height: 1;
white-space: nowrap;
}

.edu-card__title {
margin: 0;
color: #1E2430;
font-size: 18px;
line-height: 1.45;
font-weight: 500;
word-break: break-word;
}

.edu-card__desc {
margin-top: 10px;
color: #5B6472;
font-size: 14px;
line-height: 1.55;
}

.edu-card__footer {
margin-top: 14px;
}

.edu-card__more {
display: inline-flex;
align-items: center;
gap: 6px;
color: #3B4556;
font-size: 14px;
line-height: 1;
}

.edu-directions {
display: flex;
flex-direction: column;
gap: 18px;
}

.edu-search {
display: flex;
flex-direction: column;
gap: 10px;
}

.edu-search__box,
.edu-popup-search__box {
display: flex;
align-items: center;
background: #FFFFFF;
border: 1px solid #E7EAF0;
border-radius: 14px;
min-height: 52px;
padding: 0 14px;
transition: border-color .18s ease, box-shadow .18s ease;
}

.edu-search__box:focus-within,
.edu-popup-search__box:focus-within {
border-color: #C9D2E0;
box-shadow: 0 6px 18px rgba(17, 24, 39, 0.05);
}

.edu-search__icon,
.edu-popup-search__box > i {
display: inline-flex;
align-items: center;
justify-content: center;
color: #8A94A6;
font-size: 14px;
margin-right: 10px;
flex-shrink: 0;
}

.edu-search__input,
.edu-popup-search__input {
flex: 1;
width: 100%;
border: 1px solid transparent !important;
outline: none;
background: transparent;
padding: 0;
height: 50px;
font-size: 15px;
line-height: 1.2;
color: #1E2430;
box-shadow: none !important;
}

.edu-search__input::placeholder,
.edu-popup-search__input::placeholder {
color: #98A2B3;
}

.edu-search__clear,
.edu-popup-search__clear {
border: 0;
background: transparent;
padding: 0;
margin-left: 10px;
width: 22px;
height: 22px;
display: inline-flex;
align-items: center;
justify-content: center;
cursor: pointer;
color: #98A2B3;
flex-shrink: 0;
}

.edu-search__clear:hover,
.edu-popup-search__clear:hover {
color: #5B6472;
}

.edu-search__status {
font-size: 13px;
line-height: 1.4;
color: #6B7280;
padding-left: 2px;
}

.edu-popup-search {
position: relative;
margin: 0 auto 28px;
z-index: 20;
}

.edu-popup-search__dropdown {
position: absolute;
top: calc(100% + 8px);
left: 0;
right: 0;
background: #FFFFFF;
border: 1px solid #E7EAF0;
border-radius: 16px;
box-shadow: 0 16px 36px rgba(17, 24, 39, 0.12);
overflow: hidden;
z-index: 30;
}

.edu-popup-search__item {
display: block;
padding: 14px 18px;
text-decoration: none;
color: #1E2430;
border-bottom: 1px solid #F1F1F1;
}

.edu-popup-search__item:last-child {
border-bottom: 0;
}

.edu-popup-search__item:hover {
background: #F5F7FA;
}

.edu-popup-search__title {
display: block;
font-size: 15px;
font-weight: 500;
line-height: 1.35;
}

.edu-popup-search__meta {
display: block;
margin-top: 5px;
font-size: 13px;
color: #6B7280;
}

.edu-popup-search__empty {
padding: 16px 18px;
font-size: 14px;
color: #6B7280;
}

.dir-single-layout {
width: min(1200px, calc(100% - 32px));
margin: 0 auto;
}

.dir-single {
min-width: 0;
color: #1F2937;
}

.dir-single__section {
margin-top: 42px;
}

.dir-single__title {
margin: 0 0 18px;
font-size: 20px;
line-height: 32px;
font-weight: 700;
color: #FFFFFF;
}

.dir-single__h2 {
margin: 0 0 16px;
font-size: 24px;
line-height: 1.2;
font-weight: 700;
color: #222222;
}

.dir-single__text,
.dir-single__text p {
font-size: 16px;
line-height: 1.8;
color: #3F3F46;
}

.dir-hero {
background: linear-gradient(135deg, #2F65D8 0%, #16B7F0 100%);
border-radius: 18px;
padding: 26px 24px;
color: #FFFFFF;
}

.dir-hero__badge {
display: inline-flex;
align-items: center;
min-height: 24px;
padding: 0 14px;
border: 1px solid rgba(255,255,255,.65);
border-radius: 999px;
font-size: 14px;
color: #FFFFFF;
margin-bottom: 18px;
}

.dir-hero__content {
max-width: 620px;
}

.dir-hero__list {
margin: 0 0 22px 18px;
padding: 0;
color: rgba(255,255,255,.95);
}

.dir-hero__list li {
margin-bottom: 6px;
line-height: 1.6;
}

.dir-hero__buttons {
display: flex;
flex-wrap: wrap;
gap: 12px;
}

.dir-btn {
display: inline-flex;
align-items: center;
justify-content: center;
min-height: 46px;
padding: 0 18px;
border-radius: 999px;
text-decoration: none;
font-size: 15px;
font-weight: 500;
transition: opacity .2s ease, transform .2s ease;
}

.dir-btn:hover {
opacity: .92;
transform: translateY(-1px);
}

.dir-btn--ghost {
color: #FFFFFF;
border: 1px solid rgba(255,255,255,.7);
background: transparent;
}

.dir-btn--white {
color: #2F3A4C;
background: #FFFFFF;
}

.dir-steps {
display: grid;
grid-template-columns: repeat(3, minmax(0, 1fr));
gap: 16px;
}

.dir-step {
background: #F7F8FA;
border-radius: 14px;
padding: 20px 18px;
min-height: 190px;
display: flex;
flex-direction: column;
justify-content: space-between;
}

.dir-step__title {
margin: 0 0 8px;
font-size: 18px;
line-height: 1.35;
font-weight: 700;
color: #222222;
}

.dir-step__text {
font-size: 15px;
line-height: 1.65;
color: #575757;
}

.dir-step__num {
font-size: 42px;
line-height: 1;
font-weight: 700;
color: #2B2B2B;
margin-top: 14px;
}

.dir-result {
display: grid;
grid-template-columns: 1.2fr .8fr;
gap: 28px;
align-items: center;
}

.dir-result__title {
margin: 0 0 14px;
font-size: 22px;
line-height: 1.35;
font-weight: 700;
color: #222222;
}

.dir-result__list {
margin: 0;
padding-left: 18px;
}

.dir-result__list li {
margin-bottom: 10px;
font-size: 15px;
line-height: 1.7;
color: #444444;
}

.dir-result__image img {
display: block;
width: 100%;
height: auto;
border-radius: 8px;
}

.dir-application {
background: #F7F8FA;
border-radius: 16px;
padding: 24px;
}

.dir-application__grid {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 28px;
}

.dir-application__title {
margin: 0 0 10px;
font-size: 22px;
line-height: 1.35;
font-weight: 700;
color: #222222;
}

.dir-application__lead {
margin-bottom: 14px;
font-size: 15px;
line-height: 1.7;
color: #666666;
}

.dir-doc-buttons {
display: flex;
flex-direction: column;
gap: 10px;
}

.dir-doc-btn {
display: flex;
align-items: center;
justify-content: center;
min-height: 48px;
padding: 0 16px;
border: 1px solid #AEB4BF;
border-radius: 8px;
background: #FFFFFF;
color: #444444;
text-decoration: none;
font-size: 14px;
text-align: center;
}

.dir-info-table {
display: flex;
flex-direction: column;
}

.dir-info-row {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 20px;
padding: 12px 0;
border-bottom: 1px solid #E6E8ED;
}

.dir-info-row:last-child {
border-bottom: 0;
}

.dir-info-key {
color: #3A3A3A;
font-size: 15px;
line-height: 1.5;
}

.dir-info-val {
color: #2B2B2B;
font-size: 15px;
line-height: 1.5;
font-weight: 500;
}


/* Special listing for self-propelled machines */
.spm-directions {
display: flex;
flex-direction: column;
gap: 18px;
}

.spm-cards {
display: grid;
grid-template-columns: repeat(3, minmax(0, 1fr));
gap: 18px;
}

.spm-card {
position: relative;
min-height: 190px;
overflow: hidden;
border-radius: 16px;
background: linear-gradient(135deg, #E8F1FF 0%, #F8FAFC 100%);
border: 1px solid #E7EAF0;
box-shadow: 0 8px 20px rgba(17, 24, 39, 0.05);
transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}

.spm-card:hover {
transform: translateY(-3px);
border-color: #CBD5E1;
box-shadow: 0 14px 32px rgba(17, 24, 39, 0.10);
}

.spm-card__link {
position: relative;
display: flex;
min-height: 190px;
padding: 14px;
color: inherit;
text-decoration: none;
}

.spm-card__image {
position: absolute;
inset: 0;
background-size: cover;
background-position: center;
transform: scale(1.02);
transition: transform .2s ease;
}

.spm-card:hover .spm-card__image {
transform: scale(1.06);
}

.spm-card__overlay {
position: absolute;
inset: 0;
background: linear-gradient(180deg, rgba(15, 23, 42, 0.10) 0%, rgba(15, 23, 42, 0.08) 38%, rgba(15, 23, 42, 0.70) 100%);
}

.spm-card__content {
position: relative;
z-index: 1;
display: flex;
width: 100%;
flex-direction: column;
justify-content: space-between;
align-items: flex-start;
}

.spm-card__badge {
display: inline-flex;
align-items: center;
min-height: 28px;
padding: 0 10px;
font-size: 12px;
line-height: 1;
}

.spm-card__title {
margin: 0;
padding: 12px 12px 10px;
width: 100%;
border-radius: 12px;
background: rgba(255,255,255,.90);
color: #1E293B;
font-size: 18px;
line-height: 1.3;
font-weight: 500;
box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
}

.spm-single {
min-width: 0;
color: #1F2937;
}

.spm-hero {
position: relative;
overflow: hidden;
border-radius: 18px;
padding: 30px 28px;
color: #FFFFFF;
background: linear-gradient(135deg, #1F5EBE 0%, #1684C7 52%, #16B7F0 100%);
}

.spm-hero:before {
content: "";
position: absolute;
width: 280px;
height: 280px;
right: -80px;
top: -90px;
border-radius: 999px;
background: rgba(255,255,255,.16);
}

.spm-hero__content {
position: relative;
z-index: 1;
max-width: 760px;
}

.spm-hero__badge {
display: inline-flex;
align-items: center;
min-height: 28px;
padding: 0 14px;
border: 1px solid rgba(255,255,255,.55);
border-radius: 999px;
font-size: 14px;
color: #FFFFFF;
margin-bottom: 16px;
}

.spm-hero__title {
margin: 0 0 14px;
font-size: 30px;
line-height: 1.22;
font-weight: 800;
color: #FFFFFF;
}

.spm-hero__lead {
max-width: 720px;
margin: 0 0 20px;
color: rgba(255,255,255,.95);
font-size: 17px;
line-height: 1.7;
}

.spm-hero__actions {
display: flex;
flex-wrap: wrap;
gap: 12px;
}

.spm-btn {
display: inline-flex;
align-items: center;
justify-content: center;
min-height: 46px;
padding: 0 18px;
border-radius: 999px;
text-decoration: none;
font-size: 15px;
font-weight: 600;
transition: opacity .2s ease, transform .2s ease;
}

.spm-btn:hover {
opacity: .92;
transform: translateY(-1px);
}

.spm-btn--white {
background: #FFFFFF;
color: #1F3F7A;
}

.spm-btn--ghost {
border: 1px solid rgba(255,255,255,.70);
color: #FFFFFF;
background: transparent;
}

.spm-facts {
display: grid;
grid-template-columns: repeat(3, minmax(0, 1fr));
gap: 14px;
margin-top: 18px;
}

.spm-fact {
border: 1px solid rgba(255,255,255,.25);
border-radius: 14px;
padding: 14px 16px;
background: rgba(255,255,255,.12);
backdrop-filter: blur(8px);
}

.spm-fact__value {
display: block;
font-size: 24px;
line-height: 1.15;
font-weight: 800;
color: #FFFFFF;
}

.spm-fact__label {
display: block;
margin-top: 4px;
font-size: 13px;
line-height: 1.35;
color: rgba(255,255,255,.85);
}

.spm-section {
margin-top: 42px;
}

.spm-h2 {
margin: 0 0 16px;
font-size: 24px;
line-height: 1.25;
font-weight: 800;
color: #222222;
}

.spm-benefits {
display: grid;
grid-template-columns: repeat(3, minmax(0, 1fr));
gap: 16px;
}

.spm-benefit {
border-radius: 16px;
padding: 20px 18px;
background: #FFFFFF;
border: 1px solid #E7EAF0;
box-shadow: 0 8px 20px rgba(17, 24, 39, 0.05);
}

.spm-benefit__icon {
display: inline-flex;
align-items: center;
justify-content: center;
width: 42px;
height: 42px;
margin-bottom: 14px;
border-radius: 14px;
background: #EAF3FF;
color: #1F6EAE;
font-size: 18px;
}

.spm-benefit__title {
margin: 0 0 8px;
color: #1F2937;
font-size: 18px;
line-height: 1.35;
font-weight: 800;
}

.spm-benefit__text {
color: #5B6472;
font-size: 15px;
line-height: 1.65;
}

.spm-plan-grid {
display: grid;
grid-template-columns: repeat(2, minmax(0, 1fr));
gap: 16px;
}

.spm-plan-card {
position: relative;
overflow: hidden;
border-radius: 16px;
background: #FFFFFF;
border: 1px solid #E7EAF0;
box-shadow: 0 8px 20px rgba(17, 24, 39, 0.05);
padding: 18px;
}

.spm-plan-card__top {
display: flex;
align-items: flex-start;
justify-content: space-between;
gap: 14px;
margin-bottom: 14px;
}

.spm-plan-card__num {
display: inline-flex;
align-items: center;
justify-content: center;
min-width: 34px;
height: 34px;
border-radius: 12px;
background: #EAF3FF;
color: #1F6EAE;
font-weight: 800;
}

.spm-plan-card__title {
margin: 0;
color: #1F2937;
font-size: 17px;
line-height: 1.45;
font-weight: 600;
}

.spm-plan-card__total {
flex-shrink: 0;
display: inline-flex;
align-items: center;
justify-content: center;
min-height: 34px;
padding: 0 12px;
border-radius: 999px;
background: #F5F7FA;
color: #1F2937;
font-weight: 600;
white-space: nowrap;
}

.spm-hours {
display: grid;
grid-template-columns: repeat(3, minmax(0, 1fr));
gap: 8px;
}

.spm-hour {
border-radius: 12px;
padding: 10px;
background: #F8FAFC;
}

.spm-hour__label {
display: block;
color: #64748B;
font-size: 12px;
line-height: 1.2;
}

.spm-hour__value {
display: block;
margin-top: 4px;
color: #1F2937;
font-size: 16px;
line-height: 1.2;
font-weight: 600;
}


.spm-plan-grid--study {
grid-template-columns: 1fr;
gap: 18px;
}

.spm-plan-card--study {
padding: 22px;
border-radius: 22px;
}

.spm-plan-card__head {
display: flex;
align-items: flex-start;
gap: 14px;
min-width: 0;
}

.spm-plan-card__main {
min-width: 0;
}

.spm-plan-card__control {
margin-top: 8px;
font-size: 14px;
line-height: 1.4;
color: #64748B;
}

.spm-subplan {
margin-top: 18px;
display: grid;
gap: 10px;
}

.spm-subplan__item {
display: flex;
align-items: flex-start;
justify-content: space-between;
gap: 16px;
padding: 14px 16px;
border-radius: 16px;
background: #F8FAFC;
border: 1px solid rgba(148, 163, 184, 0.28);
}

.spm-subplan__main {
display: flex;
align-items: flex-start;
gap: 12px;
min-width: 0;
}

.spm-subplan__num {
flex: 0 0 auto;
min-width: 42px;
padding: 5px 9px;
border-radius: 999px;
background: #EEF2FF;
color: #3D52D5;
font-size: 13px;
font-weight: 700;
text-align: center;
}

.spm-subplan__content {
min-width: 0;
}

.spm-subplan__title {
font-size: 15px;
line-height: 1.45;
font-weight: 600;
color: #111827;
}

.spm-subplan__hours {
flex: 0 0 auto;
white-space: nowrap;
font-size: 14px;
font-weight: 700;
color: #111827;
}

.spm-summary {
display: grid;
grid-template-columns: repeat(4, minmax(0, 1fr));
gap: 12px;
margin-bottom: 18px;
}

.spm-summary__item {
border-radius: 14px;
padding: 16px;
background: #F7F8FA;
border: 1px solid #E7EAF0;
}

.spm-summary__value {
display: block;
color: #1F2937;
font-size: 24px;
line-height: 1.15;
font-weight: 600;
}

.spm-summary__label {
display: block;
margin-top: 4px;
color: #64748B;
font-size: 13px;
line-height: 1.35;
}

.spm-cta {
border-radius: 18px;
padding: 24px;
background: #F7F8FA;
border: 1px solid #E7EAF0;
}

.spm-cta__title {
margin: 0 0 10px;
color: #1F2937;
font-size: 22px;
line-height: 1.35;
font-weight: 600;
}

.spm-cta__text {
margin: 0 0 16px;
color: #5B6472;
font-size: 15px;
line-height: 1.65;
}

@media (max-width: 980px) {
.edu-layout,
.dir-single-layout,
.dir-steps,
.dir-result,
.dir-application__grid,
.spm-cards,
.spm-facts,
.spm-benefits,
.spm-plan-grid,
.spm-summary {
grid-template-columns: 1fr;
}

.edu-cards {
grid-template-columns: 1fr;
}
}

@media (max-width: 640px) {
.dir-single-layout {
width: min(100% - 24px, 1200px);
}

.dir-hero {
padding: 18px 16px;
border-radius: 14px;
}

.dir-single__title {
font-size: 24px;
line-height: 1.3;
}

.dir-single__h2 {
font-size: 28px;
line-height: 1.2;
}

.dir-hero__badge {
min-height: 32px;
font-size: 13px;
padding: 0 12px;
margin-bottom: 14px;
}

.dir-hero__buttons {
flex-direction: column;
align-items: stretch;
}

.dir-btn {
width: 100%;
min-height: 44px;
font-size: 14px;
}

.dir-step {
min-height: auto;
padding: 16px;
}

.dir-step__num {
font-size: 34px;
}

.dir-result__title,
.dir-application__title {
font-size: 20px;
}

.dir-application {
padding: 18px 16px;
border-radius: 14px;
}

.spm-hero {
padding: 22px 18px;
border-radius: 14px;
}

.spm-hero__title {
font-size: 24px;
}

.spm-hero__lead {
font-size: 15px;
}

.spm-btn {
width: 100%;
}

.spm-hours {
grid-template-columns: 1fr;
}


.spm-plan-card--study {
padding: 16px;
border-radius: 18px;
}

.spm-subplan__item {
display: block;
}

.spm-subplan__hours {
margin-top: 10px;
padding-left: 54px;
}

.dir-info-row {
grid-template-columns: 1fr;
gap: 6px;
padding: 10px 0;
}
}
CSS;

wp_add_inline_style('education-sidebar-inline', $css);
});

add_action('wp_footer', function () {
?>
<script>
  document.addEventListener('click', function (e) {
    const toggle = e.target.closest('.edu-sidebar__toggle');
    if (!toggle) return;

    const group = toggle.closest('.edu-sidebar__group');
    if (!group) return;

    group.classList.toggle('is-open');
  });
</script>
<?php
});


add_shortcode('education_breadcrumbs', function () {
    return edu_render_education_breadcrumbs();
});

add_shortcode('education_sidebar', function () {
    $sections = edu_get_sections_map();
    $current_post_id = is_singular(edu_get_post_types()) ? get_the_ID() : 0;
    $current_section_slug = '';
    $open_all = false;

    if (is_page()) {
        $current_section_slug = edu_get_section_by_page_id(get_the_ID());

        if (!$current_section_slug) {
            $current_section_slug = edu_get_section_by_page_title(get_the_title(get_the_ID()));
        }
    }

    if ($current_post_id) {
        $current_section_slug = edu_get_section_by_post_type(get_post_type($current_post_id));
    }


    ob_start();
    ?>
<aside class="edu-sidebar">
  <?php foreach ($sections as $slug => $data) : ?>
  <?php
            $posts = get_posts([
                    'post_type'      => $data['post_type'],
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'title',
  'order'          => 'ASC',
  ]);

  $is_open = $open_all || ($slug === $current_section_slug);
  ?>
  <div class="edu-sidebar__group <?php echo $is_open ? 'is-open' : ''; ?>">
    <div class="edu-sidebar__head">
      <a href="<?php echo esc_url(edu_get_section_url($slug, $data)); ?>" class="edu-sidebar__title">
        <?php echo esc_html($data['label']); ?>
      </a>

      <button type="button" class="edu-sidebar__toggle" aria-label="Открыть список">
        <span class="edu-sidebar__icon"><i class="fa-solid fa-chevron-down"></i></span>
      </button>
    </div>

    <div class="edu-sidebar__body">
      <ul class="edu-sidebar__list">
        <?php if (!empty($posts)) : ?>
        <?php foreach ($posts as $p) : ?>
        <li class="edu-sidebar__item <?php echo ($current_post_id === (int) $p->ID) ? 'is-current' : ''; ?>">
          <a href="<?php echo esc_url(get_permalink($p->ID)); ?>">
            <?php echo esc_html(get_the_title($p->ID)); ?>
          </a>
        </li>
        <?php endforeach; ?>
        <?php else : ?>
        <li class="edu-sidebar__item"><span class="edu-sidebar__empty">Пока пусто</span></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <?php endforeach; ?>
</aside>
<?php

    return ob_get_clean();
});


if (!function_exists('edu_get_direction_card_image_url')) {
    function edu_get_direction_card_image_url($post_id) {
        $thumb = get_the_post_thumbnail_url($post_id, 'large');
        if ($thumb) {
            return $thumb;
        }

        $acf_fields = ['image', 'main_image', 'cover', 'photo', 'picture'];
        foreach ($acf_fields as $field_name) {
            $image = edu_get_field($field_name, $post_id);

            if (is_array($image) && !empty($image['url'])) {
                return $image['url'];
            }

            if (is_numeric($image)) {
                $url = wp_get_attachment_image_url((int) $image, 'large');
                if ($url) {
                    return $url;
                }
            }

            if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
        }

        return '';
    }
}

if (!function_exists('edu_normalize_direction_title')) {
    function edu_normalize_direction_title($title) {
        return mb_strtolower(trim((string) $title));
    }
}

add_shortcode('direction_cards', function ($atts) {
    $atts = shortcode_atts([
            'type'  => '',
'debug' => '0',
], $atts);

$post_types = edu_get_post_types();
$selected_post_type = in_array($atts['type'], $post_types, true) ? $atts['type'] : $post_types;

$args = [
'post_type'      => $selected_post_type,
'posts_per_page' => -1,
'post_status'    => 'publish',
'orderby'        => 'date',
'order'          => 'ASC',
];

$query = new WP_Query($args);

if ($atts['debug'] === '1') {
ob_start();
echo '<pre style="font-size:12px;line-height:1.4;white-space:pre-wrap;">';
        echo esc_html(print_r($args, true));
        echo "\nFOUND POSTS: " . intval($query->found_posts);
        echo '</pre>';
return ob_get_clean();
}

if (!$query->have_posts()) {
return '<p>Программы пока не добавлены.</p>';
}

$uid = 'edu-directions-' . wp_unique_id();


if ($atts['type'] === 'self_propelled_machi') {
ob_start();
?>
<div class="spm-directions" id="<?php echo esc_attr($uid); ?>">
  <div class="edu-search">
    <div class="edu-search__box">
      <span class="edu-search__icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>

      <input type="text" class="edu-search__input" placeholder="Поиск по категориям" aria-label="Поиск по категориям">

      <button type="button" class="edu-search__clear" aria-label="Очистить поиск" hidden>
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="edu-search__status" hidden></div>
  </div>

  <div class="spm-cards">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
    <?php
                    $post_id = get_the_ID();
                    $title = get_the_title();
                    $description = trim((string) edu_get_field('description', $post_id));
                    $image_url = edu_get_direction_card_image_url($post_id);
                    $search_text = implode(' ', array_filter([
                        $title,
                        wp_strip_all_tags($description),
                        'самоходные машины',
                        'трактористы машинисты',
                    ]));
                    ?>
    <article class="spm-card" data-search="<?php echo esc_attr(mb_strtolower($search_text)); ?>">
      <a class="spm-card__link" href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <?php if ($image_url) : ?>
        <div class="spm-card__image" style="background-image:url('<?php echo esc_url($image_url); ?>');"></div>
        <?php else : ?>
        <div class="spm-card__image" style="background-image:linear-gradient(135deg,#DCEBFF 0%,#BFD7FF 45%,#F8FAFC 100%);"></div>
        <?php endif; ?>

        <div class="spm-card__overlay"></div>

        <div class="spm-card__content">
          <span class="spm-card__badge"></span>
          <h3 class="spm-card__title"><?php echo esc_html($title); ?></h3>
        </div>
      </a>
    </article>
    <?php endwhile; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('<?php echo esc_js($uid); ?>');
    if (!root) return;

    const input = root.querySelector('.edu-search__input');
    const clearBtn = root.querySelector('.edu-search__clear');
    const status = root.querySelector('.edu-search__status');
    const cards = Array.from(root.querySelectorAll('.spm-card'));

    const normalize = (value) => (value || '').toLowerCase().trim().replace(/\s+/g, ' ');

    const update = () => {
      const term = normalize(input.value);
      let visibleCount = 0;

      cards.forEach((card) => {
        const haystack = normalize(card.dataset.search || '');
        const isVisible = !term || haystack.includes(term);
        card.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount += 1;
      });

      clearBtn.hidden = !term;

      if (!term) {
        status.hidden = true;
        status.textContent = '';
        return;
      }

      status.hidden = false;
      status.textContent = visibleCount > 0 ? `Найдено: ${visibleCount}` : 'Ничего не найдено';
    };

    input.addEventListener('input', update);
    clearBtn.addEventListener('click', function () {
      input.value = '';
      update();
      input.focus();
    });
  });
</script>
<?php

        wp_reset_postdata();
        return ob_get_clean();
    }

    ob_start();
    ?>
<div class="edu-directions" id="<?php echo esc_attr($uid); ?>">
  <div class="edu-search">
    <div class="edu-search__box">
      <span class="edu-search__icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>

      <input type="text" class="edu-search__input" placeholder="Поиск по направлениям" aria-label="Поиск по направлениям">

      <button type="button" class="edu-search__clear" aria-label="Очистить поиск" hidden>
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="edu-search__status" hidden></div>
  </div>

  <div class="edu-cards">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
    <?php
                $duration     = trim((string) edu_get_field('duration'));
                $price        = trim((string) edu_get_field('price'));
                $study_format = edu_get_field('study_format');
                $description  = trim((string) edu_get_field('description'));
                $link         = get_permalink();
                $title        = get_the_title();

                if (is_array($study_format)) {
                    $study_format = implode(' / ', array_filter($study_format));
                }
                $study_format = trim((string) $study_format);

                $plain_desc = wp_strip_all_tags($description);
                if (mb_strlen($plain_desc) > 120) {
    $plain_desc = mb_substr($plain_desc, 0, 120) . '…';
    }

    $search_text = implode(' ', array_filter([
    $title,
    $plain_desc,
    $duration,
    $price,
    $study_format,
    ]));
    ?>
    <article class="edu-card" data-search="<?php echo esc_attr(mb_strtolower($search_text)); ?>">
      <a class="edu-card__link" href="<?php echo esc_url($link); ?>">
        <div class="edu-card__badges">
          <?php if ($duration) : ?>
          <span class="edu-card__badge"><?php echo esc_html($duration); ?> ч.</span>
          <?php endif; ?>

          <?php if ($price) : ?>
          <span class="edu-card__badge"><?php echo esc_html($price); ?> ₽</span>
          <?php endif; ?>

          <?php if ($study_format) : ?>
          <span class="edu-card__badge"><?php echo esc_html($study_format); ?></span>
          <?php endif; ?>
        </div>

        <h3 class="edu-card__title"><?php echo esc_html($title); ?></h3>

        <?php if ($plain_desc) : ?>
        <div class="edu-card__desc"><?php echo esc_html($plain_desc); ?></div>
        <?php endif; ?>

        <div class="edu-card__footer"><span class="edu-card__more">Подробнее</span></div>
      </a>
    </article>
    <?php endwhile; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('<?php echo esc_js($uid); ?>');
    if (!root) return;

    const input = root.querySelector('.edu-search__input');
    const clearBtn = root.querySelector('.edu-search__clear');
    const status = root.querySelector('.edu-search__status');
    const cards = Array.from(root.querySelectorAll('.edu-card'));

    const normalize = (value) => (value || '').toLowerCase().trim().replace(/\s+/g, ' ');

    const update = () => {
      const term = normalize(input.value);
      let visibleCount = 0;

      cards.forEach((card) => {
        const haystack = normalize(card.dataset.search || '');
        const isVisible = !term || haystack.includes(term);
        card.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount += 1;
      });

      clearBtn.hidden = !term;

      if (!term) {
        status.hidden = true;
        status.textContent = '';
        return;
      }

      status.hidden = false;
      status.textContent = visibleCount > 0 ? `Найдено: ${visibleCount}` : 'Ничего не найдено';
    };

    input.addEventListener('input', update);
    clearBtn.addEventListener('click', function () {
      input.value = '';
      update();
      input.focus();
    });
  });
</script>
<?php

    wp_reset_postdata();
    return ob_get_clean();
});

add_shortcode('education_popup_search', function () {
    $query = new WP_Query([
            'post_type'      => edu_get_post_types(),
'posts_per_page' => -1,
'post_status'    => 'publish',
'orderby'        => 'title',
'order'          => 'ASC',
]);

if (!$query->have_posts()) {
return '';
}

$items = [];

while ($query->have_posts()) {
$query->the_post();

$duration     = trim((string) edu_get_field('duration'));
$price        = trim((string) edu_get_field('price'));
$study_format = edu_get_field('study_format');
$description  = trim((string) edu_get_field('description'));

if (is_array($study_format)) {
$study_format = implode(' / ', array_filter($study_format));
}

$items[] = [
'title'  => get_the_title(),
'url'    => get_permalink(),
'meta'   => trim(implode(' · ', array_filter([
$duration ? $duration . ' ч.' : '',
$price ? $price . ' ₽' : '',
trim((string) $study_format),
]))),
'search' => mb_strtolower(trim(implode(' ', array_filter([
get_the_title(),
wp_strip_all_tags($description),
$duration,
$price,
$study_format,
])))),
];
}

wp_reset_postdata();

$uid = 'edu-popup-search-' . wp_unique_id();

ob_start();
?>
<div class="edu-popup-search" id="<?php echo esc_attr($uid); ?>">
  <div class="edu-popup-search__box">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>

    <input type="text" class="edu-popup-search__input" placeholder="Найти программу обучения" autocomplete="off">

    <button type="button" class="edu-popup-search__clear" aria-label="Очистить поиск" hidden>
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="edu-popup-search__dropdown" hidden></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('<?php echo esc_js($uid); ?>');
    if (!root) return;

    const input = root.querySelector('.edu-popup-search__input');
    const clearBtn = root.querySelector('.edu-popup-search__clear');
    const dropdown = root.querySelector('.edu-popup-search__dropdown');
    const items = <?php echo wp_json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    const normalize = (value) => (value || '').toLowerCase().trim().replace(/\s+/g, ' ');

    const escapeHtml = (value) => String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

    const close = () => {
      dropdown.hidden = true;
      dropdown.innerHTML = '';
    };

    const render = () => {
      const term = normalize(input.value);
      clearBtn.hidden = !term;

      if (term.length < 2) {
        close();
        return;
      }

      const results = items.filter(item => normalize(item.search).includes(term)).slice(0, 8);

      if (!results.length) {
        dropdown.hidden = false;
        dropdown.innerHTML = '<div class="edu-popup-search__empty">Ничего не найдено</div>';
        return;
      }

      dropdown.hidden = false;
      dropdown.innerHTML = results.map(item => `
            <a class="edu-popup-search__item" href="${escapeHtml(item.url)}">
              <span class="edu-popup-search__title">${escapeHtml(item.title)}</span>
              ${item.meta ? `<span class="edu-popup-search__meta">${escapeHtml(item.meta)}</span>` : ''}
            </a>
          `).join('');
    };

    input.addEventListener('input', render);

    clearBtn.addEventListener('click', function () {
      input.value = '';
      close();
      clearBtn.hidden = true;
      input.focus();
    });

    document.addEventListener('click', function (e) {
      if (!root.contains(e.target)) close();
    });
  });
</script>
<?php

    return ob_get_clean();
});