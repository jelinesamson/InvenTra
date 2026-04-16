const API_BASE = '/BSIT-2E-G1-Group1-EduTrack/Api/incomingProducts.php';
let incomingProducts = [];
let selectedCode = null;
let incomingMode = 'add';

window.addEventListener('DOMContentLoaded', () => {
  loadProductOptions();
  loadIncomingList();
  document.getElementById('incomingSearch').addEventListener('input', filterIncomingTable);
  document.getElementById('productCode').addEventListener('input', () => {
    document.getElementById('productCodeError').textContent = '';
  });
});

function loadProductOptions() {
  fetch(API_BASE, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action: 'getProductOptions' })
  })
    .then(res => res.json())
    .then(response => {
      if (response.status === 'success') {
        incomingProducts = response.data;
        const list = document.getElementById('productCodeList');
        if (list) {
          list.innerHTML = '';
          incomingProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.product_code;
            list.appendChild(option);
          });
        }
      } else {
        Swal.fire('Error', response.message || 'Could not load products', 'error');
      }
    })
    .catch(() => {
      Swal.fire('Error', 'Failed to load products', 'error');
    });
}

function loadIncomingList() {
  fetch(API_BASE, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action: 'getIncoming' })
  })
    .then(res => res.json())
    .then(response => {
      if (response.status === 'success') {
        renderIncomingTable(response.data);
      } else {
        Swal.fire('Error', response.message || 'Could not load incoming products', 'error');
      }
    })
    .catch(() => {
      Swal.fire('Error', 'Failed to load incoming products', 'error');
    });
}

function renderIncomingTable(data) {
  const tbody = document.querySelector('#incomingTable tbody');
  tbody.innerHTML = '';

  if (!data.length) {
    tbody.innerHTML = '<tr><td colspan="8">No incoming products found.</td></tr>';
    return;
  }

  data.forEach(product => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${product.product_code}</td>
      <td>${product.product_type}</td>
      <td>${product.size}</td>
      <td>${product.department}</td>
      <td>${product.quantity}</td>
      <td>${product.incoming_qty}</td>
      <td>${product.incoming_status}</td>
      <td>
        <button class="btn btn-sm btn-warning me-1" onclick="editIncoming('${product.product_code}')">Edit</button>
        <button class="btn btn-sm btn-success" onclick="receiveIncoming('${product.product_code}')">Receive</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function loadProductDetails() {
  const code = document.getElementById('productCode').value.trim();
  document.getElementById('productCodeError').textContent = '';

  if (!code) {
    document.getElementById('productCodeError').textContent = 'Enter a product code first.';
    return;
  }

  selectedCode = code;
  const product = incomingProducts.find(item => item.product_code === code);

  if (!product) {
    document.getElementById('incomingType').value = '';
    document.getElementById('incomingSize').value = '';
    document.getElementById('incomingDept').value = '';
    document.getElementById('incomingCurrentQty').value = '';
    document.getElementById('productCodeError').textContent = 'Product not found.';
    return;
  }

  document.getElementById('incomingType').value = product.product_type;
  document.getElementById('incomingSize').value = product.size;
  document.getElementById('incomingDept').value = product.department;
  document.getElementById('incomingCurrentQty').value = product.quantity;
  document.getElementById('incomingQty').value = '';
}

function submitIncoming() {
  const code = document.getElementById('productCode').value.trim();
  const amount = parseInt(document.getElementById('incomingQty').value, 10);
  const error = document.getElementById('incomingQtyError');
  error.textContent = '';

  if (!code) {
    document.getElementById('productCodeError').textContent = 'Enter a product code first.';
    return;
  }

  document.getElementById('productCodeError').textContent = '';

  if (!amount || amount < 0) {
    error.textContent = 'Enter a valid incoming quantity.';
    return;
  }

  const action = incomingMode === 'edit' ? 'editIncoming' : 'addIncoming';
  const body = new URLSearchParams({ action, code, amount });

  fetch(API_BASE, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body
  })
    .then(res => res.json())
    .then(response => {
      if (response.status === 'success') {
        Swal.fire('Success', response.message, 'success');
        const modalEl = document.getElementById('incomingModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        resetIncomingForm();
        loadProductOptions();
        loadIncomingList();
      } else {
        Swal.fire('Error', response.message || 'Unable to save incoming quantity', 'error');
      }
    })
    .catch(() => {
      Swal.fire('Error', 'Request failed', 'error');
    });
}

function editIncoming(code) {
  const product = incomingProducts.find(item => item.product_code === code);
  if (!product) {
    Swal.fire('Error', 'Product not found', 'error');
    return;
  }

  incomingMode = 'edit';
  selectedCode = code;
  document.getElementById('incomingModalTitle').textContent = 'Edit Incoming Quantity';
  document.getElementById('productCode').value = code;
  document.getElementById('productCode').disabled = true;
  loadProductDetails();
  document.getElementById('incomingQty').value = product.incoming_qty;

  const modal = new bootstrap.Modal(document.getElementById('incomingModal'));
  modal.show();
}

function receiveIncoming(code) {
  const product = incomingProducts.find(item => item.product_code === code);
  if (!product) {
    Swal.fire('Error', 'Product not found', 'error');
    return;
  }
  if ((product.incoming_qty || 0) <= 0) {
    Swal.fire('Info', 'This product has no incoming quantity to receive.', 'info');
    return;
  }

  Swal.fire({
    title: 'Receive product?',
    text: 'This will move incoming quantity to current stock.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2a5d9f',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Receive'
  }).then(result => {
    if (!result.isConfirmed) return;
    fetch(API_BASE, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'receive', code })
    })
      .then(res => res.json())
      .then(response => {
        if (response.status === 'success') {
          Swal.fire('Success', response.message, 'success');
          loadProductOptions();
          loadIncomingList();
        } else {
          Swal.fire('Error', response.message || 'Unable to receive stock', 'error');
        }
      })
      .catch(() => {
        Swal.fire('Error', 'Request failed', 'error');
      });
  });
}

function filterIncomingTable() {
  const query = document.getElementById('incomingSearch').value.toLowerCase();
  document.querySelectorAll('#incomingTable tbody tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
}

function resetIncomingForm() {
  incomingMode = 'add';
  selectedCode = null;
  document.getElementById('incomingModalTitle').textContent = 'Add Incoming Quantity';
  document.getElementById('productCode').value = '';
  document.getElementById('productCode').disabled = false;
  document.getElementById('incomingType').value = '';
  document.getElementById('incomingSize').value = '';
  document.getElementById('incomingDept').value = '';
  document.getElementById('incomingCurrentQty').value = '';
  document.getElementById('incomingQty').value = '';
  document.getElementById('incomingQtyError').textContent = '';
  document.getElementById('productCodeError').textContent = '';
}

const incomingModalEl = document.getElementById('incomingModal');
incomingModalEl.addEventListener('hidden.bs.modal', resetIncomingForm);
