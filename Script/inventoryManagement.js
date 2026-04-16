    // Inventory Management Script
    const API_BASE = '/BSIT-2E-G1-Group1-EduTrack/Api/inventoryManagement.php';

    // Stores last fetched journal data for CSV export
    let currentData = [];

    // ── Init ──
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts();
    });

    // Load products into dropdown
    function loadProducts() {
    fetch(API_BASE + '?action=getProducts')
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success') {
                const select = document.getElementById('productSelect');
                select.innerHTML = '<option value="">-- Select Product --</option>';
                
                response.data.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.name;
                    select.appendChild(option);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: response.message || 'Failed to load products.',
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load products. Please check your connection or server.',
            });
            console.error('Failed to load products:', err);
        });
        
}
    

    // Validate selection, fetch journal entries, render results
    function searchJournal() {
        const prodId   = document.getElementById('productSelect').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo   = document.getElementById('dateTo').value;

        if (!prodId) {
            Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Please select a product first.',
            confirmButtonColor: '#2a5d9f'
        });
        return;
        }

        showStatus('Loading...');

        // Build API URL with query params
        let url = API_BASE + '?action=getJournal&prod_id=' + encodeURIComponent(prodId);
        if (dateFrom) url += '&date_from=' + encodeURIComponent(dateFrom);
        if (dateTo)   url += '&date_to=' + encodeURIComponent(dateTo);

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    console.log(response.data); 
                    currentData = response.data;

                    if (currentData.length === 0) {
                        showStatus('No records found for this product.');
                        hideSummaryCards();
                    } else {
                        renderTable(currentData);
                        renderSummary(currentData);
                        updatePrintHeader();
                    }
                } else {
                    showStatus(response.message || 'An error occurred.');
                    hideSummaryCards();
                }
            })
            .catch(err => {
                console.error('Failed to fetch journal:', err);
                showStatus('Failed to load data. Please try again.');
                hideSummaryCards();
            });
    }

    function capitalizeWords(str) {
        return str
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

// Render journal entries into the table
    function renderTable(data) {
        const tbody = document.getElementById('journalBody');
        tbody.innerHTML = '';

        data.forEach((entry, index) => {
            const tr = document.createElement('tr');

            // Product name
            const tdProd = document.createElement('td');
            tdProd.textContent = entry.prod_name;
            tr.appendChild(tdProd);

            // Incoming qty
            const tdIncoming = document.createElement('td');
            tdIncoming.textContent = parseInt(entry.incoming_quantity) || 0;
            tr.appendChild(tdIncoming);

            // Sales qty
            const tdSales = document.createElement('td');
            tdSales.textContent = parseInt(entry.sales) || 0;
            tr.appendChild(tdSales);

            // Notes badge
            const tdNotes = document.createElement('td');
            const badge = document.createElement('span');
            badge.className = 'badge badge-' + entry.notes.toLowerCase();
            badge.textContent = entry.notes;
            tdNotes.appendChild(badge);
            tr.appendChild(tdNotes);

            // Total quantity 
            const tdTotalQty = document.createElement('td');
            tdTotalQty.textContent = entry.total_qty;
            tr.appendChild(tdTotalQty);

            // Date/Time
            const tdDate = document.createElement('td');
            tdDate.textContent = formatDateTime(entry.date_time);
            tr.appendChild(tdDate);

            // Created by
            const tdBy = document.createElement('td');
            tdBy.textContent = entry.account_name ? capitalizeWords(entry.account_name) : '—';
            tr.appendChild(tdBy);

            tbody.appendChild(tr);
        });
    }

// Compute summary values and show cards
    function renderSummary(data) {
        let totalSales = 0;
        let currentStock = 0;
        let currentIncoming = 0;

        data.forEach(entry => {
            totalSales += parseInt(entry.sales) || 0;
        });

        if (data.length > 0) {
            const latest = data[0];
            currentStock = parseInt(latest.total_qty) || 0;
            currentIncoming = parseInt(latest.current_incoming_qty) || 0;
        }

        document.getElementById('cardIncoming').textContent = currentIncoming;
        document.getElementById('cardSales').textContent = totalSales;
        document.getElementById('cardStock').textContent = currentStock;

        document.getElementById('summaryCards').classList.add('visible');
    }

    // Hide summary cards
    function hideSummaryCards() {
        document.getElementById('summaryCards').classList.remove('visible');
    }

    // Show status message in table body
    function showStatus(message) {
        const tbody = document.getElementById('journalBody');
        tbody.innerHTML = '<tr class="status-row"><td colspan="8">' + message + '</td></tr>';
    }

    // Format MySQL timestamp to readable date
    function formatDateTime(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric'
        }) + ' ' + d.toLocaleTimeString('en-US', {
            hour: 'numeric', minute: '2-digit', hour12: true
        });
    }

    // Update print header with product name and date range
    function updatePrintHeader() {
        const select = document.getElementById('productSelect');
        const prodName = select.options[select.selectedIndex].text;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo   = document.getElementById('dateTo').value;

        document.getElementById('printTitle').textContent = 'Inventory Journal — ' + prodName;

        let dateRange = '';
        if (dateFrom && dateTo) {
            dateRange = 'Period: ' + dateFrom + ' to ' + dateTo;
        } else {
            dateRange = 'All records';
        }
        document.getElementById('printDateRange').textContent = dateRange;
    }

    // Trigger browser print dialog
    function printPage() {
        const tbody = document.getElementById('journalBody');
            if (!tbody || tbody.children.length === 0 || (tbody.children.length === 1 && tbody.children[0].classList.contains('status-row'))) {
            Swal.fire({
                icon: 'info',
                title: 'Nothing to print',
                text: 'Please search for a product first.',
                confirmButtonColor: '#2a5d9f'
            });
            return; // stop printing
        }
        window.print();
    }

    // Export journal data as CSV download
    function exportCSV() {
        if (!currentData || currentData.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No data',
                text: 'Please search for a product first.',
                confirmButtonColor: '#2a5d9f'
            });
            return;
        }

        // CSV header
        const headers = ['#', 'Product', 'Incoming', 'Sales', 'Notes', 'T. Qty', 'Date / Time', 'By'];
        const rows = [headers.join(',')];

        // Build data rows
        currentData.forEach((entry, index) => {
            const incoming = (parseInt(entry.incoming_quantity) > 0 || entry.notes === 'Add' || entry.notes === 'Edit' || entry.notes === 'Receive') ? (entry.incoming_quantity || 0) : '';
            const sales    = (parseInt(entry.sales) > 0 || entry.notes.startsWith('Sale')) ? (entry.sales || 0) : '';

            const row = [
                index + 1,
                '"' + (entry.prod_name || '').replace(/"/g, '""') + '"',
                incoming,
                sales,
                entry.notes,
                entry.total_qty,
                '"' + formatDateTime(entry.date_time) + '"',
                '"' + (entry.account_name || '').replace(/"/g, '""') + '"'
            ];
            rows.push(row.join(','));
        });

    // Create blob and trigger download
    const csvContent = rows.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    // Build filename: inventory_{product}_{date}.csv
    const select = document.getElementById('productSelect');
    const prodName = select.options[select.selectedIndex].text
        .toLowerCase().replace(/\s+/g, '_');
    const today = new Date().toISOString().split('T')[0];
    const filename = 'inventory_' + prodName + '_' + today + '.csv';

    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

function autoLoadJournal() {
    const prodId = document.getElementById('productSelect').value;

    if (!prodId) return;

    showStatus('Loading...');

    let url = API_BASE + '?action=getJournal&prod_id=' + encodeURIComponent(prodId);

    fetch(url)
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success') {
                currentData = response.data;

                if (currentData.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No records',
                        text: 'No records found for this product.',
                        confirmButtonColor: '#2a5d9f'
                    });
                    hideSummaryCards();
                    return;
                } else {
                    renderTable(currentData);
                    renderSummary(currentData);
                    updatePrintHeader();
                }
            } else {
                showStatus(response.message || 'Error loading data.');
                hideSummaryCards();
            }
        })
        .catch(err => {
            console.error(err);
            showStatus('Failed to load data.');
        });
}

document.getElementById('productSelect').addEventListener('change', () => {
    autoLoadJournal();
});
