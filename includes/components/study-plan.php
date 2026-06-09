<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('edu_render_study_plan_table')) {
function edu_render_study_plan_table($post_id) {
if (!function_exists('have_rows') || !have_rows('study_plan_rows', $post_id)) {
return '';
}

$rows = [];

while (have_rows('study_plan_rows', $post_id)) {
the_row();

$component_name = edu_get_sub_field('component_name');
$hours_raw      = edu_get_sub_field('hours');
$control_form   = edu_get_sub_field('control_form');

$component_name = is_scalar($component_name) ? trim((string) $component_name) : '';
$hours_output   = is_scalar($hours_raw) ? trim((string) $hours_raw) : '';

if (is_array($control_form)) {
if (isset($control_form['label'])) {
$control_form = $control_form['label'];
} elseif (isset($control_form['value'])) {
$control_form = $control_form['value'];
} else {
$control_form = implode(', ', array_filter(array_map('strval', $control_form)));
}
}

$control_form = is_scalar($control_form) ? trim((string) $control_form) : '';

if ($component_name === '' && $hours_output === '' && $control_form === '') {
continue;
}

$rows[] = [
'component_name' => $component_name,
'hours'          => $hours_output,
'control_form'   => $control_form,
];
}

if (empty($rows)) {
return '';
}

ob_start();
?>
<section class="spm-section" id="program">
  <h2 class="spm-h2">Учебный план</h2>

  <div class="spm-plan-grid">
    <?php foreach ($rows as $index => $row) : ?>
    <article class="spm-plan-card">
      <div class="spm-plan-card__top">
        <div style="display:flex;gap:12px;align-items:flex-start;">
          <span class="spm-plan-card__num"><?php echo esc_html($index + 1); ?></span>
          <h3 class="spm-plan-card__title"><?php echo esc_html($row['component_name'] ?: '—'); ?></h3>
        </div>

        <?php if ($row['hours'] !== '') : ?>
        <span class="spm-plan-card__total"><?php echo esc_html($row['hours']); ?> ч.</span>
        <?php endif; ?>
      </div>

      <div class="spm-hours">
        <div class="spm-hour">
          <span class="spm-hour__label">Трудоёмкость</span>
          <span class="spm-hour__value"><?php echo esc_html($row['hours'] !== '' ? $row['hours'] . ' ч.' : '—'); ?></span>
        </div>

        <div class="spm-hour">
          <span class="spm-hour__label">Форма контроля</span>
          <span class="spm-hour__value"><?php echo esc_html($row['control_form'] ?: '—'); ?></span>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>
<?php

        return ob_get_clean();
    }
}
