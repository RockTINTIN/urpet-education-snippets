<?php

if (!defined('ABSPATH')) {
    exit;
}

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
