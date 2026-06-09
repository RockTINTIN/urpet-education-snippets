# URPET Education Snippets

Плагин переносит два WordPress Code Snippets в файловую структуру.

## Структура

- `urpet-education-snippets.php` — главный файл плагина и константы.
- `includes/helpers.php` — общие ACF/section helpers.
- `includes/education-common.php` — загрузчик общих компонентов.
- `includes/direction-single-page.php` — загрузчик компонентов страницы направления.
- `includes/components/assets.php` — подключение CSS/JS.
- `includes/components/breadcrumbs.php` — шорткод `[education_breadcrumbs]`.
- `includes/components/sidebar.php` — шорткод `[education_sidebar]`.
- `includes/components/direction-cards.php` — шорткод `[direction_cards]`.
- `includes/components/popup-search.php` — шорткод `[education_popup_search]`.
- `includes/components/study-plan.php` — компонент учебного плана.
- `includes/components/direction-single-page.php` — шорткод `[direction_single_page]`.
- `assets/css/education.css` — стили плагина.
- `assets/js/sidebar.js` — поведение сайдбара.

## Шорткоды

- `[education_breadcrumbs]`
- `[education_sidebar]`
- `[direction_cards type="prof-perepodgotovka"]`
- `[direction_cards type="self_propelled_machi"]`
- `[education_popup_search]`
- `[direction_single_page]`

## Установка

1. Загрузить папку `urpet-education-snippets` в `/wp-content/plugins/`.
2. Активировать плагин в админке WordPress.
3. Отключить старые сниппеты в Code Snippets/WPCode, чтобы не было дублей.
4. Редактировать файлы через WebStorm и заливать по SFTP.
