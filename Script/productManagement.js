let API = '/BSIT-2E-G1-Group1-EduTrack/Api/productManagement.php';
let table;
let rowIndex = null;
let isEditMode = false;
let deletedProducts = [];

// ================= TYPE =================
function checkType(select) {
    const isOther = select.value === "Other";
    if (isOther) {
        $("#otherTypeContainer").show();
    } else {
        $("#otherTypeContainer").hide();
        $("#otherType").val("");
    }
}

function checkSizeByType() {
    let type = $("#type").val();

    if (type === "Uniform" || type === "Merchandise") {
        $("#size").prop("disabled", false);
        $("#size option[value='None']").hide();
        $("#size").val("");
    } else {
        $("#size option[value='None']").show();
        $("#size").val("None").prop("disabled", true);
    }
}

function setTypeValue(type) {
    const standardTypes = ["ID Lace", "Book", "Uniform", "Merchandise"];
    if (standardTypes.includes(type)) {
        $("#type").val(type);
        $("#otherTypeContainer").hide();
        $("#otherType").val("");
    } else {
        $("#type").val("Other");
        $("#otherTypeContainer").show();
        $("#otherType").val(type);
    }
    checkSizeByType();
}

// ================= DATATABLE =================
$(document).ready(function () {
    table = $('#myTable').DataTable({
        ajax: {
            url: API,
            type: "POST",
            data: { action: "get" },
            dataSrc: "data"
        },
        dom: '<"top-bar"fB>t',
        buttons: [
            {
                extend: 'collection',
                text: 'Export',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                container: $('body')
            }
        ],
        paging: false,
        info: false,
        lengthChange: false,
        ordering: false
    });
});

// ================= MODAL =================
function openModal() {
  const modal = new bootstrap.Modal(document.getElementById('productModal'));
  modal.show();
}

function closeModal() {
  const modalEl = document.getElementById('productModal');
  const modal = bootstrap.Modal.getInstance(modalEl);
  if (modal) modal.hide();
  clearForm();
}

// ================= VALIDATION =================
function validateForm() {
  let isValid = true;
  $("small.text-danger").text("");

  let code = $("#code").val().trim();
  let typeSel = $("#type").val();
  let other = $("#otherType").val().trim();
  let dept = $("#dept").val();
  let price = $("#price").val().trim();
  let status = $("#status").val();

  if (!code) {
    $("#codeError").text("Product code is required");
    isValid = false;
  }

  if (!typeSel) {
    $("#typeError").text("Type is required");
    isValid = false;
  } else if (typeSel === "Other" && !other) {
    $("#otherTypeError").text("Please specify the type");
    isValid = false;
  }

  if (!dept) {
    $("#deptError").text("Department is required");
    isValid = false;
  }

  if (!status) {
    $("#statusError").text("Status is required");
    isValid = false;
  }

  if (!price) {
    $("#priceError").text("Price is required");
    isValid = false;
  } else if (isNaN(price) || parseFloat(price) < 0) {
    $("#priceError").text("Enter a valid price");
    isValid = false;
  }

  return isValid;
}

$("#productForm input, #productForm select").on("input change", function() {
  let id = $(this).attr("id");
  $("#" + id + "Error").text("");
});

const productModalEl = document.getElementById('productModal');
productModalEl.addEventListener('hidden.bs.modal', function () {
  clearForm();
  $(".text-danger").text("");
  rowIndex = null;
  isEditMode = false;
});

// ================= SAVE =================
function store() {
  if (!validateForm()) return;

  let code = $("#code").val().trim();
  let product_type = $("#type").val();
  let other = $("#otherType").val().trim();
  let size = $("#size").val();
  let department = $("#dept").val();
  let quantity = 0;
  let price = parseFloat($("#price").val()) || 0;
  let status = $("#status").val();
  let incoming_qty = 0;

  let type = (product_type === "Other") ? other : product_type;
  if (type !== "Uniform" && type !== "Merchandise") {
    size = "None";
  }

  let exists = false;
  table.rows().every(function () {
    if (this.data()[0] === code) exists = true;
  });
  if (exists && rowIndex === null) {
    $("#codeError").text("Product code already exists");
    return;
  }

  let payload = {
    code: code,
    product_type: type,
    size: size,
    department: department,
    quantity: quantity,
    incoming_qty: incoming_qty,
    price: price,
    status: status
  };

  let action = isEditMode ? "update" : "store";

  Swal.fire({
    title: 'Processing...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  $.ajax({
    url: API,
    type: 'POST',
    data: {
      payload: JSON.stringify(payload),
      action: action
    },
    dataType: 'json',
    success: function(response) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: response.message,
        confirmButtonColor: '#2a5d9f'
      });
      closeModal();
      lucide.createIcons();
      table.ajax.reload();
    },
    error: function() {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong!'
      });
    }
  });
}

// ================= EDIT =================
function update(btn) {
  let row = $(btn).closest('tr');
  rowIndex = table.row(row);
  let data = rowIndex.data();

  isEditMode = true;
  openModal();

  $("#productModal .modal-title").text("Edit Product");
  $("#saveProductBtn").text("Update");

  $("#code").val(data[0]).prop("disabled", true);
  setTypeValue(data[1]);
  checkSizeByType();
  $("#size").val(data[2]).prop("disabled", false);
  $("#dept").val(data[3]).prop("disabled", false);
  $("#price").val(data[4]).prop("disabled", false);
  $("#status").val((data[5] || '').toString().toLowerCase()).prop("disabled", false);
}

// ================= RECEIVE STOCK =================
function receiveStock(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();
  let code = data[0];

  $.ajax({
    url: API,
    type: 'POST',
    data: {
      action: 'getProduct',
      code: code
    },
    dataType: 'json',
    success: function(response) {
      if (response.status !== 'success') {
        Swal.fire('Error', response.message || 'Product not found', 'error');
        return;
      }

      let incoming_qty = parseInt(response.data.incoming_qty) || 0;
      if (incoming_qty <= 0) {
        Swal.fire({
          icon: 'info',
          title: 'No incoming stock',
          text: 'There is no incoming stock to receive for this product.'
        });
        return;
      }

      Swal.fire({
        title: 'Receive stock?',
        text: 'This will add incoming stock.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2a5d9f',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, receive it'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: API,
            type: 'POST',
            data: {
              action: 'receive',
              code: code
            },
            dataType: 'json',
            success: function(res) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: res.message
              });
              table.ajax.reload();
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error receiving stock'
              });
            }
          });
        }
      });
    },
    error: function() {
      Swal.fire('Error', 'Failed to load product details', 'error');
    }
  });
}

// ================= DELETE =================
function deleteRow(btn) {
  let row = $(btn).closest('tr');
  let data = table.row(row).data();
  let code = data[0];

  Swal.fire({
    title: 'Are you sure?',
    text: 'Delete this product?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: API,
        type: 'POST',
        data: {
          action: 'drop',
          code: code
        },
        dataType: 'json',
        success: function(res) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: res.message
          });
          table.ajax.reload();
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error deleting product'
          });
        }
      });
    }
  });
}

// ================= DELETED PRODUCTS =================
function viewDeleted() {
  $.ajax({
    url: API,
    type: 'POST',
    data: { action: 'getDeleted' },
    dataType: 'json',
    success: function(response) {
      console.log(response);
      if (response.status === 'success') {
        deletedProducts = response.data;
        renderDeletedProducts(deletedProducts);
        const deletedModal = new bootstrap.Modal(document.getElementById('deletedModal'));
        deletedModal.show();
      } else {
        Swal.fire('Error', response.message || 'Failed to load deleted products', 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Could not load deleted products', 'error');
    }
  });
}

function renderDeletedProducts(data) {
  const tbody = document.querySelector('#deletedTable tbody');
  tbody.innerHTML = '';

  if (!data.length) {
    tbody.innerHTML = '<tr><td colspan="6">No deleted products found.</td></tr>';
    return;
  }

  data.forEach(product => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${product.product_code}</td>
      <td>${product.product_type}</td>
      <td>${product.size}</td>
      <td>${product.department}</td>
      <td>${product.price}</td>
      <td>
        <button class="btn btn-sm btn-success me-1" onclick="restoreProduct('${product.product_code}')">Restore</button>
        <button class="btn btn-sm btn-danger" onclick="permanentDelete('${product.product_code}')">Permanent Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function filterDeletedProducts() {
  const query = document.getElementById('deletedSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#deletedTable tbody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
}

function restoreProduct(code) {
  Swal.fire({
    title: 'Restore this product?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2a5d9f',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, restore'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: API,
        type: 'POST',
        data: {
          action: 'restore',
          code: code
        },
        dataType: 'json',
        success: function(response) {
          Swal.fire('Restored!', response.message, 'success');
          table.ajax.reload();
          viewDeleted();
        },
        error: function() {
          Swal.fire('Error', 'Could not restore product', 'error');
        }
      });
    }
  });
}

function permanentDelete(code) {
  Swal.fire({
    title: 'Permanently delete this product?',
    text: 'This cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete permanently'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: API,
        type: 'POST',
        data: {
          action: 'permanentDelete',
          code: code
        },
        dataType: 'json',
        success: function(response) {
          Swal.fire('Deleted!', response.message, 'success');
          table.ajax.reload();
          viewDeleted();
        },
        error: function() {
          Swal.fire('Error', 'Could not permanently delete product', 'error');
        }
      });
    }
  });
}

function clearForm() {
  $("#productForm")[0].reset();
  $("#otherTypeContainer").hide();
  $(".text-danger").text("");
  $("input, select").prop("disabled", false);
  isEditMode = false;
  $("#saveProductBtn").text("Save");
  $("#productModal .modal-title").text("Product");
}
