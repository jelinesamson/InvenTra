
'use strict';

const API = '../Api/salesReport.php';

// ── Formatters ────────────────────────────────────────────────────────────────
const peso = n => '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2 });
const num  = n => Number(n).toLocaleString('en-PH');
const chg  = (v, el) => {
  const up = v >= 0;
  el.textContent = (up ? '↑ ' : '↓ ') + Math.abs(v) + '% vs last month';
  el.className   = 'kpi-sub ' + (up ? 'up' : 'down');
};
const catCls = c => 'cat cat-' + String(c).replace(/\s+/g, '-');
const fmt = d => new Date(d).toLocaleString('en-PH', {
  month: 'short', day: 'numeric', year: 'numeric',
  hour: 'numeric', minute: '2-digit', hour12: true
});

// ── State ─────────────────────────────────────────────────────────────────────
let data = null;

// ── Fetch ─────────────────────────────────────────────────────────────────────
async function load(month, type) {
  const params = new URLSearchParams();
  if (month) params.set('month', month);
  if (type)  params.set('type',  type);
  const url = API + (params.toString() ? '?' + params : '');

  const res  = await fetch(url);
  const json = await res.json();
  if (!json.success) throw new Error(json.error);
  return json;
}

// ── Populate selects ──────────────────────────────────────────────────────────
function fillMonths(months, selected) {
  const sel = document.getElementById('selMonth');
  sel.innerHTML = '';
  months.forEach(m => {
    const lbl = new Date(m + '-02').toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    const o   = Object.assign(document.createElement('option'), { value: m, textContent: lbl });
    if (m === selected) o.selected = true;
    sel.appendChild(o);
  });
}

function fillTypes(types, selected) {
  const sel = document.getElementById('selType');

  if (sel.options.length <= 1) {
    types.forEach(t => {
      const o = document.createElement('option');
      o.value = t;
      o.textContent = t;
      sel.appendChild(o);
    });
  }

  sel.value = selected;
}


// ── Render KPI ────────────────────────────────────────────────────────────────
function renderKPI(kpi) {
  document.getElementById('kTotal').textContent = peso(kpi.total_revenue);
  document.getElementById('kQty').textContent   = num(kpi.total_qty);
  document.getElementById('kTop').textContent   = kpi.top_category;
  document.getElementById('kVat').textContent   = peso(kpi.total_vat);
  chg(kpi.rev_change_pct, document.getElementById('kTotalChg'));
  chg(kpi.qty_change_pct, document.getElementById('kQtyChg'));
}

// ── Render Product Summary ────────────────────────────────────────────────────
function renderSummary(items, overall) {
  const body = document.getElementById('bodySummary');
  const foot = document.getElementById('footSummary');
  body.innerHTML = '';
  foot.innerHTML = '';

  if (!items.length) {
    body.innerHTML = '<tr><td colspan="7" class="empty-state">No sales found for this period.</td></tr>';
    return;
  }

  let totQty = 0, totAmt = 0, totVat = 0;

  items.forEach((r, i) => {
    const qty = parseInt(r.total_qty);
    const amt = parseFloat(r.total_amount);
    const vat = parseFloat(r.total_vat);
    totQty += qty; totAmt += amt; totVat += vat;

    body.innerHTML += `
      <tr>
        <td>${i + 1}</td>
        <td><b>${r.product}</b></td>
        <td><span class="${catCls(r.category)}">${r.category}</span></td>
        <td>${peso(r.unit_price)}</td>
        <td>${num(qty)}</td>
        <td><b>${peso(amt)}</b></td>
        <td>${peso(vat)}</td>
      </tr>`;
  });

  // Overall Total row
  foot.innerHTML = `
    <tr class="total-row">
      <td colspan="4"><b>Overall Total Amount</b></td>
      <td><b>${num(totQty)}</b></td>
      <td><b>${peso(totAmt)}</b></td>
      <td><b>${peso(totVat)}</b></td>
    </tr>`;
}

// ── Render Transaction History ────────────────────────────────────────────────
function renderHistory(history) {
  const body = document.getElementById('bodyHistory');
  const foot = document.getElementById('footHistory');
  body.innerHTML = '';
  foot.innerHTML = '';

  if (!history.length) {
    body.innerHTML = '<tr><td colspan="11" class="empty-state">No transactions found.</td></tr>';
    return;
  }

  let totTotal = 0, totPaid = 0, totChange = 0, totVat = 0;
  let processedTxns = new Set(); // Prevent duplicating transaction-level money

  history.forEach((r, i) => {
    const tp  = parseFloat(r.total_price);
    totTotal += tp; 

    // Only add Paid, Change, and VAT once per transaction ID
    if (!processedTxns.has(r.transaction_id)) {
        totPaid += parseFloat(r.paid);
        totChange += parseFloat(r.change);
        totVat += parseFloat(r.vat_amount);
        processedTxns.add(r.transaction_id);
    }

    body.innerHTML += `
      <tr>
        <td>${i + 1}</td>
        <td><b style="color: #0056b3;">${r.transaction_id}</b></td> <td><b>${r.product}</b></td>
        <td><span class="${catCls(r.category)}">${r.category}</span></td>
        <td>${r.qty}</td>
        <td>${peso(r.unit_price)}</td>
        <td><b>${peso(tp)}</b></td>
        <td>${peso(r.paid)}</td>
        <td>${peso(r.change)}</td>
        <td>${peso(r.vat_amount)}</td>
        <td style="white-space:nowrap;font-size:12px">${fmt(r.sold_at)}</td>
        <td class="st-${r.status}">${r.status}</td>
      </tr>`;
  });

  // Overall Total Money row 
  foot.innerHTML = `
    <tr class="total-row">
      <td colspan="6"><b>Overall Total Amount (Sales)</b></td>
      <td><b>${peso(totTotal)}</b></td>
      <td><b>${peso(totPaid)}</b></td>
      <td><b>${peso(totChange)}</b></td>
      <td><b>${peso(totVat)}</b></td>
      <td colspan="2"></td>
    </tr>`;
}

// ── CSV Export ────────────────────────────────────────────────────────────────
function exportCSV() {
  if (!data) return;

  let csv = 'EduTrack Sales Report — ' + data.month_label + '\r\n\r\n';

  // Summary sheet
  csv += 'PRODUCT SUMMARY\r\n';
  csv += '#,Product,Category,Unit Price,Total Qty,Total Amount,VAT (12%)\r\n';
  let sumAmt = 0, sumVat = 0;
  data.items.forEach((r, i) => {
    sumAmt += parseFloat(r.total_amount);
    sumVat += parseFloat(r.total_vat);
    csv += [i+1, `"${r.product}"`, r.category, r.unit_price,
            r.total_qty, r.total_amount, parseFloat(r.total_vat).toFixed(2)].join(',') + '\r\n';
  });
  csv += `Overall Total,,,,, ${sumAmt.toFixed(2)}, ${sumVat.toFixed(2)}\r\n\r\n`;

  // History sheet
  csv += 'TRANSACTION HISTORY\r\n';
  csv += '#,Product,Category,Qty,Unit Price,Total Price,Paid,Change,VAT,Date,Status\r\n';
  data.history.forEach((r, i) => {
    csv += [i+1, `"${r.product}"`, r.category, r.qty, r.unit_price,
            r.total_price, r.paid, r.change,
            parseFloat(r.vat_amount).toFixed(2),
            `"${r.sold_at}"`, r.status].join(',') + '\r\n';
  });

  const blob = new Blob([csv], { type: 'text/csv' });
  const a    = Object.assign(document.createElement('a'), {
    href: URL.createObjectURL(blob),
    download: 'EduTrack_SalesReport_' + data.month + '.csv'
  });
  a.click();
}

// ── Main render ───────────────────────────────────────────────────────────────
async function render(month, type) {
  try {
    data = await load(month, type);

    fillMonths(data.available_months, data.month);
    fillTypes(data.product_types, type || '');

    document.getElementById('lblGenerated').textContent =
      'Generated: ' + data.generated;

    renderKPI(data.kpi);
    renderSummary(data.items, data.overall_total);
    renderHistory(data.history);

    lucide.createIcons();
  } catch (e) {
    console.error(e);
    alert('Error loading report: ' + e.message);
  }
}

// ── Events ────────────────────────────────────────────────────────────────────
// Search TXN ID Live Filter
document.getElementById('searchTxn').addEventListener('input', function () {
  if (!data) return;
  const term = this.value.trim().toLowerCase();
  
  if (term === '') {
    renderHistory(data.history); // Reset to full data
  } else {
    // Filter rows that contain the typed TXN ID
    const filtered = data.history.filter(r => 
      r.transaction_id.toLowerCase().includes(term)
    );
    renderHistory(filtered);
  }
});
document.getElementById('selMonth').addEventListener('change', function () {
  render(this.value, document.getElementById('selType').value);
});
document.getElementById('selType').addEventListener('change', function () {
  render(document.getElementById('selMonth').value, this.value);
});
document.getElementById('btnCSV').addEventListener('click', exportCSV);

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
  });
});

// ── Init ──────────────────────────────────────────────────────────────────────
render('', '');
