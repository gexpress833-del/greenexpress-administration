<script>
document.addEventListener('DOMContentLoaded', function () {
    const rate = parseFloat(@json(\App\Models\ExchangeRate::current()));
    if (!rate || isNaN(rate)) return;

    function toFixedTwo(v) {
        return (Math.round(v * 100) / 100).toFixed(2);
    }

    function wireForm(form) {
        const price = form.querySelector('[name="price"]');
        const priceFc = form.querySelector('[name="price_fc"]');
        const currency = form.querySelector('[name="currency"]');
        const priceUsd = form.querySelector('[name="price_usd"]');

        // ensure hidden fields exist when needed
        if (!priceFc && price) {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'price_fc';
            form.appendChild(h);
        }

        if (!priceUsd && price && currency && currency.value === 'fc') {
            const h2 = document.createElement('input');
            h2.type = 'hidden';
            h2.name = 'price_usd';
            form.appendChild(h2);
        }

        const p = form.querySelector('[name="price"]');
        const pf = form.querySelector('[name="price_fc"]');
        const pu = form.querySelector('[name="price_usd"]');

        // create visible conversion labels next to inputs
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
        // attach rate badge next to currency select
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

        const rateBadge = attachRateBadge(currency);

        let editing = null;
        const debounce = (fn, ms = 150) => {
            let t;
            return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
        };

        function updateFromPrice() {
            if (!p || !pf) return;
            const v = parseFloat(p.value || 0);
            if (isNaN(v)) { pf.value = ''; if (pu) pu.value = ''; return; }
            const cur = currency ? currency.value : 'usd';
            if (cur === 'usd') {
                pf.value = toFixedTwo(v * rate);
                if (pu) pu.value = toFixedTwo(v);
                if (labelPrice) labelPrice.textContent = '≈ ' + toFixedTwo(v * rate) + ' FC';
                if (labelPriceFc) labelPriceFc.textContent = '≈ $ ' + toFixedTwo(v);
            } else {
                // price input is in FC when currency=fc
                pf.value = toFixedTwo(v);
                if (pu) pu.value = toFixedTwo(v / rate);
                if (labelPrice) labelPrice.textContent = '≈ $ ' + toFixedTwo(v / rate);
                if (labelPriceFc) labelPriceFc.textContent = '≈ ' + toFixedTwo(v) + ' FC';
            }
        }

        function updateFromPriceFc() {
            if (!p || !pf) return;
            const v = parseFloat(pf.value || 0);
            if (isNaN(v)) { p.value = ''; if (pu) pu.value = ''; return; }
            const cur = currency ? currency.value : 'usd';
            if (cur === 'usd') {
                // price_fc edited, update price (usd)
                p.value = toFixedTwo(v / rate);
                if (pu) pu.value = toFixedTwo(p.value);
                if (labelPrice) labelPrice.textContent = '≈ ' + toFixedTwo(v) + ' FC';
                if (labelPriceFc) labelPriceFc.textContent = '≈ $ ' + toFixedTwo(v / rate);
            } else {
                // currency=fc, price should be fc; update price (fc -> kept)
                p.value = toFixedTwo(v);
                if (pu) pu.value = toFixedTwo(v / rate);
                if (labelPrice) labelPrice.textContent = '≈ $ ' + toFixedTwo(v / rate);
                if (labelPriceFc) labelPriceFc.textContent = '≈ ' + toFixedTwo(v) + ' FC';
            }
        }

        const debouncedPrice = debounce(() => { if (editing==='price') return; editing='price'; updateFromPrice(); editing = null; });
        const debouncedFc = debounce(() => { if (editing==='price_fc') return; editing='price_fc'; updateFromPriceFc(); editing = null; });

        if (p) p.addEventListener('input', debouncedPrice);
        if (pf) pf.addEventListener('input', debouncedFc);

        if (currency) currency.addEventListener('change', function () {
            updateFromPrice();
        });

        // initial sync
        updateFromPrice();
    }

    document.querySelectorAll('form').forEach(form => {
        // only wire forms that have price or price_fc inputs
        if (form.querySelector('[name="price"]') || form.querySelector('[name="price_fc"]')) {
            wireForm(form);
        }
    });
});
</script>
