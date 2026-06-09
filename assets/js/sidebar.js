document.addEventListener('click', function (e) {
  const toggle = e.target.closest('.edu-sidebar__toggle');
  if (!toggle) return;

  const group = toggle.closest('.edu-sidebar__group');
  if (!group) return;

  group.classList.toggle('is-open');
});
