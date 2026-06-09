<?php

if (!defined('ABSPATH')) {
    exit;
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

add_shortcode('education_breadcrumbs', function () {
    return edu_render_education_breadcrumbs();
});
