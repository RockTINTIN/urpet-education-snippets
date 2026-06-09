<?php

if (!defined('ABSPATH')) {
    exit;
}

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
