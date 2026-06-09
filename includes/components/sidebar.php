<?php

if (!defined('ABSPATH')) {
    exit;
}

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
