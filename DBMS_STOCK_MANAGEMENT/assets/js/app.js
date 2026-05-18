document.addEventListener('change', (event) => {
  if (event.target.matches('.request-check')) {
    const row = event.target.closest('tr');
    const qty = row.querySelector('.request-qty');
    const justification = row.querySelector('.request-justification');
    qty.disabled = !event.target.checked;
    if (justification) justification.disabled = !event.target.checked;
    if (event.target.checked && Number(qty.value) < 1) qty.value = 1;
  }
});

document.addEventListener('input', (event) => {
  if (event.target.matches('[data-filter-table]')) {
    const term = event.target.value.toLowerCase();
    document.querySelectorAll(event.target.dataset.filterTable + ' tbody tr').forEach((row) => {
      row.hidden = !row.textContent.toLowerCase().includes(term);
    });
  }
});
