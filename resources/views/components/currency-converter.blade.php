<script>
document.addEventListener('DOMContentLoaded', function () {
    const rate = parseFloat(@json(\App\Models\ExchangeRate::current()));
    if (!rate || isNaN(rate)) return;

    function roundValue(v, decimals) {
        const factor = Math.pow(10, decimals);
        return (Math.round(v * factor) / factor).toFixed(decimals);
    }

    function wireForm(form) {
        const p = form.querySelector('[name="price"]');
        const pf = form.querySelector('[name="price_fc"]');
        const currency = form.querySelector('[name="currency"]');

        if (!p && !pf) return;

        // Disable HTML5 step validation to avoid "valeur correcte" errors
        [p, pf].forEach(inp => { if (inp) { inp.setAttribute('step', 'any'); inp.setAttribute('inputmode', 'decimal'); } });

        function attachLabel(inp) {
            if (!inp) return null;
            let el = inp.parentElement.querySelector('.converted-label');
            if (!el) {
                el = document.createElement('div');
                el.className = 'converted-label text-xs text-gray-500 dark:text-gray-400 mt-1';
                inp.parentElement.appendChild(el);
            }
            return el;
        }

        const labelPrice = attachLabel(p);
        const labelPriceFc = attachLabel(pf);

        function attachRateBadge(sel) {
            if (!sel) return null;
            let badge = sel.parentElement.querySelector('.rate-badge');
            if (!badge) {
                badge = document.createElement('div');
                badge.className = 'rate-badge text-xs text-gray-600 dark:text-gray-300 mt-1';
                sel.parentElement.appendChild(badge);
            }
            badge.textContent = 'Taux: 1 USD = ' + rate + ' FC';
            return badge;
        }

        attachRateBadge(currency);

        let updating = false;

        function sync(source) {
            if (updating) return;
            updating = true;

            const cur = currency ? currency.value : 'usd';

            if (source === 'price' && p) {
                const v = parseFloat(p.value);
                if (isNaN(v)) { if (pf) pf.value = ''; updateLabels('', ''); }
                else if (cur === 'usd') {
                    if (pf) pf.value = roundValue(v * rate, 2);
                    updateLabels(v, v * rate);
                } else {
                    if (pf) pf.value = roundValue(v, 2);
                    updateLabels(v / rate, v);
                }
            } else if (source === 'price_fc' && pf) {
                const v = parseFloat(pf.value);
                if (isNaN(v)) { if (p) p.value = ''; updateLabels('', ''); }
                else if (cur === 'usd') {
                    if (p) p.value = roundValue(v / rate, 2);
                    updateLabels(v / rate, v);
                } else {
                    if (p) p.value = roundValue(v, 2);
                    updateLabels(v / rate, v);
                }
            } else {
                // currency change or initial sync: recalculate labels from price
                const v = parseFloat(p ? p.value : 0);
                if (!isNaN(v)) {
                    if (cur === 'usd') updateLabels(v, v * rate);
                    else updateLabels(v / rate, v);
                }
            }

            updating = false;
        }

        function updateLabels(usd, fc) {
            if (labelPrice) labelPrice.textContent = usd !== '' ? '≈ $ ' + roundValue(usd, 2) : '';
            if (labelPriceFc) labelPriceFc.textContent = fc !== '' ? '≈ ' + roundValue(fc, 2) + ' FC' : '';
        }

        if (p) p.addEventListener('input', () => sync('price'));
        if (pf) pf.addEventListener('input', () => sync('price_fc'));
        if (currency) currency.addEventListener('change', () => sync('currency'));

        sync('currency');
    }

    document.querySelectorAll('form').forEach(form => {
        if (form.querySelector('[name="price"]') || form.querySelector('[name="price_fc"]')) {
            wireForm(form);
        }
    });
});
</script>
