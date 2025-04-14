<div class="header"></div>
<div class="data-table">
  <div class="success-message" id="success-message" style="display: none">
    <div class="success-text">
      <p>Dummy Text</p>
      <a id="success-message-cross" style="cursor: pointer">
        <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
      </a>
    </div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
  </div>
  <h1 class="heading"> Quản lí <span>NHÀ CUNG CẤP</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
        <option value="supplier_name">Tên</option>
        <option value="contact_phone">Số điện thoại</option>
        <option value="address">Địa chỉ</option>
        <option value="publisher">Nhà xuất bản</option>
        <option value="status_id">Trạng thái</option>
    </select>
      </div>
      <div class="search">
        <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
      </div>
    </div>
    <div class="toolbar-button-wrapper">
      <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="6" data-action="Thêm">
        <span>Thêm nhà cung cấp</span>
        <i class="bx bx-plus-medical"></i>
      </button>
    </div>
  </div>

  <div id="selected-products"></div>


  <div class="table-container">
    <div class="no-products">
      <p>Có vẻ hiện tại bạn chưa có thể loại nào?</p>
    </div>

    <table class="table" id="data-table">
      <thead>
        <tr>
        <th data-id="supplier_id">ID</th>
        <th data-id="supplier_name">Tên nhà cung cấp</th>
        <th data-id="contact_phone">Số điện thoại nhà cung cấp</th>
        <th data-id="address">Địa chỉ nhà cung cấp</th>
        <th data-id="publisher">Nhà xuất bản</th>
        <th data-id="status_id">Trạng thái</th>
          <th class="actionsTH">Actions</th>
        </tr>
      </thead>
      <tbody id="table-body"></tbody>
    </table>
  </div>
</div>
<!-- Script để fetch và hiển thị dữ liệu -->

<script>
document.addEventListener('DOMContentLoaded', function() {
      // Biến toàn cục
      let suppliers = []; // Dữ liệu gốc, không thay đổi

      // Hàm chuyển status_id thành văn bản
      function getStatusText(statusId) {
          switch (statusId) {
              case "1": return 'Active';
              case "2": return 'Inactive';
              default: return 'N/A';
          }
      }

      // Hàm thêm sự kiện lọc và tìm kiếm
      function addFilterEventListener() {
          const searchEl = document.getElementById("search-text");
          const filterOptionsEl = document.getElementById("filter-options");

          if (!searchEl || !filterOptionsEl) {
              console.error('Required elements not found: #search-text or #filter-options');
              return;
          }

          searchEl.addEventListener("input", () => {
              const filterBy = filterOptionsEl.value;
              const searchValue = searchEl.value.trim();

              let filteredData = suppliers;

              if (searchValue !== "") {
                  filteredData = suppliers.filter((supplier) => {
                      if (typeof supplier[filterBy] === "string") {
                          return supplier[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                      } else {
                          return supplier[filterBy].toString().includes(searchValue);
                      }
                  });
              }

              renderTable(filteredData);
          });

          filterOptionsEl.addEventListener("change", () => {
              searchEl.value = "";
              renderTable(suppliers);
          });
      }

      // Hàm render bảng
      function renderTable(displayedSuppliers) {
          const tableBody = document.getElementById('table-body');
          const noProductsEl = document.querySelector('.no-products');

          if (!tableBody || !noProductsEl) {
              console.error('Required elements not found: #table-body or .no-products');
              return;
          }

          tableBody.innerHTML = '';
          const activeSuppliers = displayedSuppliers.filter(supplier => supplier.status_id !== 6);

          if (activeSuppliers.length > 0) {
              noProductsEl.style.display = 'none';
              activeSuppliers.forEach((supplier, index) => {
                  const row = document.createElement('tr');
                  row.innerHTML = `
                      <td>${index + 1}</td>
                      <td>${supplier.supplier_name || 'N/A'}</td>
                      <td>${supplier.contact_phone || 'N/A'}</td>
                      <td>${supplier.address || 'N/A'}</td>
                      <td>${supplier.publisher || 'N/A'}</td>
                      <td>${getStatusText(supplier.status_id)}</td>
                      <td class="actions">
                          <div class="dropdown">
                              <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                              <div class="dropdown-content">
                                  <a href="#" class="viewSupplier" data-permission-id="6" data-action="Xem" data-supplier-id="${supplier.supplier_id}">View Supplier <i class="fa fa-eye"></i></a>
                                  <a href="#" class="editSupplier" data-permission-id="6" data-action="Sửa" data-supplier-id="${supplier.supplier_id}">Edit Supplier <i class="fa fa-edit"></i></a>
                                  <a href="#" class="deleteSupplier" data-permission-id="6" data-action="Xóa" data-supplier-id="${supplier.supplier_id}">Delete Supplier <i class="fa fa-trash"></i></a>
                              </div>
                          </div>
                      </td>
                  `;
                  tableBody.appendChild(row);
              });
          } else {
              noProductsEl.style.display = 'flex';
              tableBody.innerHTML = '<tr><td colspan="7">No suppliers found.</td></tr>';
          }
      }

      // Fetch dữ liệu ban đầu từ server
      fetch('quanlinhacungcap/fetch_ncc.php')
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  suppliers = data.data;
                  console.log('Initial suppliers:', suppliers);
                  renderTable(suppliers);
                  addFilterEventListener();
              } else {
                  console.error('Error:', data.message);
                  document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Error loading suppliers.</td></tr>';
              }
          })
          .catch(error => {
              console.error('Fetch error:', error);
              document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Error loading suppliers.</td></tr>';
          });

      // Sử dụng event delegation để xử lý các hành động
      document.getElementById('table-body').addEventListener('click', (e) => {
          const target = e.target.closest('a');
          if (!target) return;

          e.preventDefault();
          const supplierId = target.getAttribute('data-supplier-id');
          const supplier = suppliers.find(sup => sup.supplier_id === supplierId);

          if (!supplier) {
              console.error('Supplier not found:', supplierId);
              return;
          }

          if (target.classList.contains('viewSupplier')) {
              const viewModalEl = document.getElementById("view-modal");
              addModalData(viewModalEl, supplier, "innerHTML");
              viewModalEl.showModal();
          } else if (target.classList.contains('editSupplier')) {
              const editModalEl = document.getElementById("edit-modal");
              openEditModal(supplier);
          } else if (target.classList.contains('deleteSupplier')) {
              const deleteModalEl = document.getElementById("delete-modal");
              deleteModalEl.setAttribute("data-supplier-id", supplierId);
              deleteModalEl.showModal();
          }
      });

      // Hàm mở modal chỉnh sửa
      function openEditModal(supplier) {
          const editModal = document.getElementById('edit-modal');
          const form = document.getElementById('modal-edit-form');

          document.getElementById('modal-edit-supplier-id').value = supplier.supplier_id;
          document.getElementById('modal-edit-name').value = supplier.supplier_name || '';
          document.getElementById('modal-edit-contact-phone').value = supplier.contact_phone || '';
          document.getElementById('modal-edit-address').value = supplier.address || '';
          document.getElementById('modal-edit-publisher').value = supplier.publisher || '';
          document.getElementById('modal-edit-status').value = supplier.status_id;

          clearFormErrors(form);
          form.removeEventListener('submit', handleEditSubmit);
          form.addEventListener('submit', handleEditSubmit);
          editModal.showModal();
      }

      // Hàm xử lý submit form chỉnh sửa
      function handleEditSubmit(e) {
          e.preventDefault();
          const form = document.getElementById('modal-edit-form');
          const editModal = document.getElementById('edit-modal');
          const errorContainer = editModal.querySelector('.modal-error') || document.createElement('p');

          clearFormErrors(form);
          if (!errorContainer.parentElement) {
              editModal.querySelector('.modal-buttons').insertAdjacentElement('beforebegin', errorContainer);
          }
          errorContainer.textContent = '';
          errorContainer.style.display = 'none';

          const isError = validateModalFormInputs(form);
          if (isError) {
              
              errorContainer.style.display = 'block';
              errorContainer.style.color = 'var(--clr-error)';
              editModal.scrollTop = 0;
              return;
          }

          updateSupplier(form);
      }

      // Hàm cập nhật nhà cung cấp
      function updateSupplier(form) {
          const formData = new FormData(form);
          fetch('../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.json();
          })
          .then(result => {
              const editModal = document.getElementById('edit-modal');
              if (result.status === 'success') {
                  fetch('quanlinhacungcap/fetch_ncc.php')
                      .then(response => response.json())
                      .then(data => {
                          suppliers = data.data;
                          renderTable(suppliers);
                          editModal.close();
                          const successMessage = document.getElementById('success-message');
                          successMessage.querySelector('.success-text p').textContent = result.message || 'Nhà cung cấp đã được cập nhật';
                          successMessage.style.display = 'block';
                          setTimeout(() => {
                              successMessage.style.display = 'none';
                          }, 3000);
                      })
                      .catch(error => console.error('Có lỗi khi lấy dữ liệu nhà cung cấp:', error));
              } else {
                  const errorContainer = editModal.querySelector('.modal-error');
                  errorContainer.textContent = result.message || 'Có lỗi khi cập nhật nhà cung cấp';
                  errorContainer.style.display = 'block';
                  errorContainer.style.color = 'var(--clr-error)';
                  editModal.scrollTop = 0;
              }
          })
          .catch(error => {
              console.error('Cập nhật nhà cung cấp thất bại:', error);
              const editModal = document.getElementById('edit-modal');
              editModal.scrollTop = 0;
          });
      }

      // Hàm thêm nhà cung cấp
      function addProduct(formEl) {
          const formData = new FormData(formEl);
          fetch('../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.json();
          })
          .then(result => {
              const addProductModal = document.getElementById("add-modal");
              if (result.status === 'success') {
                  fetch('quanlinhacungcap/fetch_ncc.php')
                      .then(response => response.json())
                      .then(data => {
                          suppliers = data.data;
                          renderTable(suppliers);
                          addProductModal.close();
                          const successMessage = document.getElementById('success-message');
                          successMessage.querySelector('.success-text p').textContent = result.message || 'Nhà cung cấp thêm thành công';
                          successMessage.style.display = 'block';
                          setTimeout(() => {
                              successMessage.style.display = 'none';
                          }, 3000);
                      })
                      .catch(error => console.error('Có lỗi khi lấy dữ liệu nhà cung cấp:', error));
              } else {
                  const errorContainer = addProductModal.querySelector('.modal-error');
                  errorContainer.textContent = result.message || 'Có lỗi khi thêm nhà cung cấp';
                  errorContainer.style.display = 'block';
                  errorContainer.style.color = 'var(--clr-error)';
                  addProductModal.scrollTop = 0;
              }
          })
          .catch(error => {
              console.error('Thêm nhà cung cấp thất bại:', error);
              const addProductModal = document.getElementById("add-modal");
              addProductModal.scrollTop = 0;
          });
      }

      // Hàm xóa nhà cung cấp (cập nhật status_id thành 6)
      function deleteProduct(supplierId) {
          const formData = new FormData();
          formData.append('supplier_id', supplierId);
          formData.append('status_id', 6);

          fetch('../../BackEnd/Model/quanlinhacungcap/xulinhacungcap.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(result => {
              if (result.status === 'success') {
                  fetch('quanlinhacungcap/fetch_ncc.php')
                      .then(response => response.json())
                      .then(data => {
                          suppliers = data.data;
                          renderTable(suppliers);
                          const deleteModalEl = document.getElementById('delete-modal');
                          deleteModalEl.close();
                          const successMessage = document.getElementById('success-message');
                          successMessage.querySelector('.success-text p').textContent = result.message || 'Nhà cung cấp đã được đánh dấu xóa';
                          successMessage.style.display = 'block';
                          setTimeout(() => {
                              successMessage.style.display = 'none';
                          }, 3000);
                      })
                      .catch(error => console.error('Có lỗi khi lấy dữ liệu nhà cung cấp:', error));
              } else {
                  const successMessage = document.getElementById('success-message');
                  successMessage.querySelector('.success-text p').textContent = result.message || 'Xóa thất bại';
                  successMessage.style.display = 'block';
                  successMessage.style.backgroundColor = 'var(--clr-error)';
                  setTimeout(() => {
                      successMessage.style.display = 'none';
                      successMessage.style.backgroundColor = '';
                  }, 3000);
              }
          })
          .catch(error => {
              console.error('Lỗi khi gửi yêu cầu xóa:', error);
              const successMessage = document.getElementById('success-message');
              successMessage.querySelector('.success-text p').textContent = 'Lỗi khi gửi yêu cầu xóa';
              successMessage.style.display = 'block';
              successMessage.style.backgroundColor = 'var(--clr-error)';
              setTimeout(() => {
                  successMessage.style.display = 'none';
                  successMessage.style.backgroundColor = '';
              }, 3000);
          });
      }

      // Event listener cho nút xóa trong delete-modal
      const deleteModalEl = document.getElementById('delete-modal');
      const deleteDeleteButton = deleteModalEl.querySelector('#delete-delete-button');
      deleteDeleteButton.addEventListener('click', () => {
          const supplierId = parseInt(deleteModalEl.getAttribute('data-supplier-id'));
          deleteProduct(supplierId);
      });

      // Hàm xử lý modal Add
      function addViewProductModalEventListener() {
          const addProductModal = document.getElementById("add-modal");
          const formEl = document.getElementById("modal-add-form");
          const addCloseButton = addProductModal.querySelector("#add-close-button");
          const addProductToolbar = document.querySelector("#add-product-toolbar");

          addProductToolbar.addEventListener("click", () => {
              addProductModal.showModal();
          });

          addCloseButton.addEventListener("click", () => {
              addProductModal.close();
          });

          formEl.addEventListener("submit", (e) => {
              e.preventDefault();
              const isError = validateAddModalFormInputs(formEl);
              if (!isError) {
                  addProduct(formEl);
              } else {
                  addProductModal.scrollTop = 0;
              }
          });
      }

      // Hàm xóa lỗi form
      function clearFormErrors(form) {
          const errorEls = form.querySelectorAll('.modal-error');
          errorEls.forEach(errorEl => errorEl.textContent = '');
          const inputs = form.querySelectorAll('input, textarea, select');
          inputs.forEach(input => input.style.border = '');
      }

      // Hàm hiển thị lỗi form
      function displayFormErrors(errors) {
          if (!errors) return;
          Object.keys(errors).forEach(key => {
              const errorEl = document.getElementById(`modal-edit-${key}-error`);
              if (errorEl) {
                  errorEl.textContent = errors[key];
                  const input = document.getElementById(`modal-edit-${key}`);
                  if (input) input.style.border = '1px solid var(--clr-error)';
              }
          });
      }

      // Hàm validate form cho edit-modal
      function validateModalFormInputs(form) {
          const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
          let isError = false;

          inputs.forEach(input => {
              const value = input.value.trim();
              const errorEl = input.parentElement.querySelector('.modal-error');
              input.style.border = '';
              if (errorEl) errorEl.textContent = '';

              if (!value) {
                  isError = true;
                  input.style.border = '1px solid var(--clr-error)';
                  if (errorEl) errorEl.textContent = 'Trường này không được để trống!';
                  return;
              }

              if (input.id === 'modal-edit-name') {
                  if (!/^[a-zA-Z\s-]+$/.test(value)) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Tên nhà cung cấp chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                  } else if (value.length > 50) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Tên nhà cung cấp không được vượt quá 50 ký tự';
                  }
              }

              if (input.id === 'modal-edit-contact-phone') {
                  if (!/^[0-9]{10,11}$/.test(value)) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Số điện thoại phải chứa 10-11 chữ số';
                  }
              }

              if (input.id === 'modal-edit-address') {
                  if (value.length > 100) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Địa chỉ không được vượt quá 100 ký tự';
                  }
              }

              if (input.id === 'modal-edit-publisher') {
                  if (value.length > 50) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Nhà xuất bản không được vượt quá 50 ký tự';
                  }
              }
          });

          return isError;
      }

      // Hàm validate form cho add-modal
      function validateAddModalFormInputs(form) {
          const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
          let isError = false;

          inputs.forEach(input => {
              const value = input.value.trim();
              const errorEl = input.parentElement.querySelector('.modal-error');
              input.style.border = '';
              if (errorEl) errorEl.textContent = '';

              if (!value) {
                  isError = true;
                  input.style.border = '1px solid var(--clr-error)';
                  if (errorEl) errorEl.textContent = 'Trường này không được để trống!';
                  return;
              }

              if (input.id === 'modal-add-name') {
                  if (!/^[a-zA-Z\s-]+$/.test(value)) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Tên nhà cung cấp chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                  } else if (value.length > 50) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Tên nhà cung cấp không được vượt quá 50 ký tự';
                  }
              }

              if (input.id === 'modal-add-contact-phone') {
                  if (!/^[0-9]{10,11}$/.test(value)) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Số điện thoại phải chứa 10-11 chữ số';
                  }
              }

              if (input.id === 'modal-add-address') {
                  if (value.length > 100) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Địa chỉ không được vượt quá 100 ký tự';
                  }
              }

              if (input.id === 'modal-add-publisher') {
                  if (value.length > 50) {
                      isError = true;
                      input.style.border = '1px solid var(--clr-error)';
                      if (errorEl) errorEl.textContent = 'Nhà xuất bản không được vượt quá 50 ký tự';
                  }
              }
          });

          return isError;
      }

      // Hàm thêm dữ liệu vào modal
      function addModalData(modalEl, supplier, type) {
          if (type === "innerHTML") {
              modalEl.querySelector("#modal-view-supplier-id").textContent = supplier.supplier_id || 'N/A';
              modalEl.querySelector("#modal-view-name").textContent = supplier.supplier_name || 'N/A';
              modalEl.querySelector("#modal-view-contact-phone").textContent = supplier.contact_phone || 'N/A';
              modalEl.querySelector("#modal-view-address").textContent = supplier.address || 'N/A';
              modalEl.querySelector("#modal-view-publisher").textContent = supplier.publisher || 'N/A';
              modalEl.querySelector("#modal-view-status").textContent = getStatusText(supplier.status_id);
          } else if (type === "value") {
              modalEl.querySelector("#modal-edit-supplier-id").value = supplier.supplier_id;
              modalEl.querySelector("#modal-edit-name").value = supplier.supplier_name || '';
              modalEl.querySelector("#modal-edit-contact-phone").value = supplier.contact_phone || '';
              modalEl.querySelector("#modal-edit-address").value = supplier.address || '';
              modalEl.querySelector("#modal-edit-publisher").value = supplier.publisher || '';
              modalEl.querySelector("#modal-edit-status").value = supplier.status_id;
          }
      }

      // Hàm xử lý modal
      function addModalCloseButtonEventListeners() {
          document.addEventListener('click', (e) => {
              const closeEl = e.target.closest('.modal-close');
              if (closeEl) {
                  const modalId = closeEl.dataset.id;
                  const modalEl = document.getElementById(modalId);
                  if (modalEl) {
                      modalEl.close();
                      const formEl = modalEl.querySelector('form.modal-form');
                      if (formEl) {
                          clearFormErrors(formEl);
                      }
                  }
              }
          });
      }

      function addModalCancelButtonEventListener(modalEl) {
          const cancelButton = modalEl.querySelector('[id$="-close-button"]');
          if (!cancelButton) {
              console.error('Cancel button with id ending in "-close-button" not found in modal!');
              return;
          }

          cancelButton.addEventListener("click", () => {
              modalEl.close();
              const formEl = modalEl.querySelector('form.modal-form');
              if (formEl) {
                  clearFormErrors(formEl);
              }
          });
      }

      // Gọi hàm để thêm sự kiện
      addViewProductModalEventListener();
      addModalCloseButtonEventListeners();
      const addModal = document.getElementById('add-modal');
      if (addModal) {
          addModalCancelButtonEventListener(addModal);
      }
      const editModal = document.getElementById('edit-modal');
      if (editModal) {
          addModalCancelButtonEventListener(editModal);
      }
      const viewModal = document.getElementById('view-modal');
      if (viewModal) {
          addModalCancelButtonEventListener(viewModal);
      }
      const deleteModal = document.getElementById('delete-modal');
      if (deleteModal) {
          addModalCancelButtonEventListener(deleteModal);
      }
  });
  </script>
</div>
    

    <?php
        include 'quanlinhacungcap/themnhacungcap.php'; // Add Modal
        include 'quanlinhacungcap/suanhacungcap.php'; // Edieg Modal
        include 'quanlinhacungcap/xemnhacungcap.php';
        include 'quanlinhacungcap/xoanhacungcap.php';
    ?>

</div>