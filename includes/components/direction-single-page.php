<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('direction_single_page', function () {
    if (!is_singular(edu_get_post_types())) {
        return '';
    }

    $post_id = get_the_ID();
    if (!$post_id) {
        return '';
    }

    $post_type      = get_post_type($post_id);
    $is_self_propelled_machines = $post_type === 'self_propelled_machi';

    $title          = trim((string) get_the_title($post_id));
    $description    = trim((string) edu_get_field('description', $post_id));
    $study_level    = edu_get_field('study_level', $post_id);
    $duration       = trim((string) edu_get_field('duration', $post_id));
    $price          = trim((string) edu_get_field('price', $post_id));
    $study_format   = edu_get_field('study_format', $post_id);
    $final_document = trim((string) edu_get_field('final_document', $post_id));

    if (is_array($study_level)) {
        $study_level = implode(', ', array_filter($study_level));
    }

    if (is_array($study_format)) {
        $study_format = implode(' / ', array_filter($study_format));
    }

    $study_level  = trim((string) $study_level);
    $study_format = trim((string) $study_format);

    $section_label = edu_get_section_label_by_post_type($post_type);
    if (!$section_label) {
        $section_label = 'Программа обучения';
    }

    $application_form_url_inidividual   = 'https://urpet96.ru/wp-content/uploads/2026/06/Zayavlenie_na_obuchenie_individ.docx';
    $application_form_url_organisation  = 'https://urpet96.ru/wp-content/uploads/2026/06/Zayavka_na_obuchenie_esli_ot_organizatsii.docx';

    // Картинка выдаваемого документа для направления "Самоходные машины".
    // Замените URL ниже на нужный файл из медиабиблиотеки WordPress.
    $self_propelled_document_image_url = 'https://urpet96.ru/wp-content/uploads/2026/06/2025-05-15_11-58-50_winscan_to_pdf.pdf';

    ob_start();
    ?>

<div class="edu-layout dir-single-layout">

  <div class="dir-single">
    <?php echo shortcode_exists('education_breadcrumbs') ? do_shortcode('[education_breadcrumbs]') : ''; ?>
    <section class="dir-hero">
      <div class="dir-hero__content">
        <div class="dir-hero__badge"><?php echo esc_html($section_label); ?></div>

        <h1 class="dir-single__title"><?php echo esc_html($title); ?></h1>

        <ul class="dir-hero__list">
          <li>Документ с внесением в ФИС ФРДО</li>
          <li>Лекции, презентации</li>
          <li>Ответы на вопросы от специалистов</li>
        </ul>

        <div class="dir-hero__buttons">
          <a class="dir-btn dir-btn--ghost" href="<?php echo esc_url(get_permalink($post_id) . '#program'); ?>">
            Программа обучения
          </a>
          <a class="dir-btn dir-btn--white" href="<?php echo esc_url(get_permalink($post_id) . '#application'); ?>">
            Записаться на обучение
          </a>
        </div>
      </div>
    </section>

    <section class="dir-single__section">
      <h2 class="dir-single__h2">Как проходит обучение</h2>

      <div class="dir-steps">
        <div class="dir-step">
          <div>
            <div class="dir-step__title">Договор и оплата</div>
            <div class="dir-step__text">Заключение договора на обучение. Стоимость курсов определяется индивидуально при консультации после отправки заявки. Предусмотрены скидки на групповое обучение.</div>
          </div>
          <div class="dir-step__num">1</div>
        </div>

        <div class="dir-step">
          <div>
            <div class="dir-step__title">Обучение</div>
            <div class="dir-step__text">Прохождение теоретических и практических занятий в очном или дистанционном формате. Освоение материала и выполнение итоговых заданий.</div>
          </div>
          <div class="dir-step__num">2</div>
        </div>

        <div class="dir-step">
          <div>
            <div class="dir-step__title">Получение документов</div>
            <div class="dir-step__text">После успешного завершения обучения выдаётся документ установленного образца. Возможна отправка почтой или транспортной компанией.</div>
          </div>
          <div class="dir-step__num">3</div>
        </div>
      </div>
    </section>

    <?php echo edu_render_study_plan_table($post_id); ?>


    <section class="dir-single__section" id="application">
      <h2 class="dir-single__h2">Поступить на программу</h2>

      <div class="dir-application">
        <div class="dir-application__grid">
          <div>
            <h3 class="dir-application__title">Оформление заявки</h3>
            <div class="dir-application__lead">Форматы: очно или дистанционно. Возможна отправка пакета документов. Заполненные документы можно отправить на электронную почту организации.</div>

            <div class="dir-doc-buttons">
              <a class="dir-doc-btn" href="<?php echo esc_url($application_form_url_inidividual); ?>" target="_blank" rel="noopener">Скачать форму заявки на обучение</a>
              <a class="dir-doc-btn" href="<?php echo esc_url($application_form_url_organisation); ?>" target="_blank" rel="noopener">Скачать форму заявки на обучение от организации</a>
            </div>
          </div>

          <div>
            <h3 class="dir-application__title">Основная информация</h3>

            <div class="dir-info-table">


              <div class="dir-info-row">
                <div class="dir-info-key">Формат обучения</div>
                <div class="dir-info-val">очно, очно - заочная с применением дистанционных технологий</div>
              </div>

              <div class="dir-info-row">
                <div class="dir-info-key">Срок освоения программы</div>
                <div class="dir-info-val"><?php echo esc_html($duration ?: '—'); ?></div>
              </div>

              <div class="dir-info-row">
                <div class="dir-info-key">Выдаваемый документ об окончании обучения</div>
                <div class="dir-info-val">
                  <?php if ($is_self_propelled_machines && $self_propelled_document_image_url) : ?>
                  <a href="<?php echo esc_url($self_propelled_document_image_url); ?>" target="_blank" rel="noopener">
                    Открыть документ
                  </a>
                  <?php else : ?>
                  <?php echo esc_html($final_document ?: '—'); ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <section class="dir-single__section">
        <h2 class="dir-single__h2">О программе</h2>
        <div class="dir-single__text">
          <?php echo wp_kses_post(wpautop($description ?: 'Описание программы пока не заполнено.')); ?>
        </div>
      </section>
    </section>
  </div>

  <?php echo shortcode_exists('education_sidebar') ? do_shortcode('[education_sidebar]') : ''; ?>

</div>
<?php

    return ob_get_clean();
});
