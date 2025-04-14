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
  <h1 class="heading"> Quản lí <span>TÀI KHOẢN</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
          <option value="account_name">Tên tài khoản</option>
          <option value="email">Email</option>
          <option value="full_name">Họ tên</option>
          <option value="role_id">Chức vụ</option>
          <option value="status_id">Trạng thái</option>
        </select>
      </div>
      <div class="search">
        <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
      </div>
    </div>
    <div class="toolbar-button-wrapper">
      <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="3" data-action="Thêm">
        <span>Thêm tài khoản</span>
        <i class="bx bx-plus-medical"></i>
      </button>
    </div>
  </div>

  <div id="selected-products"></div>

  <div class="table-container">
    <div class="no-products">
      <p>Có vẻ hiện tại bạn chưa có tài khoản nào?</p>
    </div>

    <table class="table" id="data-table">
      <thead>
        <tr>
          <th data-id="account_id">ID</th>
          <th data-id="account_name">Tên tài khoản</th>
          <th data-id="email">Email</th>
          <th data-id="full_name">Họ tên</th>
          <th data-id="role_id">Chức vụ</th>
          <th data-id="status_id">Trạng thái</th>
          <th class="actionsTH">Actions</th>
        </tr>
      </thead>
      <tbody id="table-body"></tbody>
    </table>
  </div>

  <!-- Script để fetch và hiển thị dữ liệu -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Biến toàn cục
    let accounts = []; // Dữ liệu gốc, không thay đổi

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

        let filteredData = accounts;

        if (searchValue !== "") {
          filteredData = accounts.filter((account) => {
            if (typeof account[filterBy] === "string") {
              return account[filterBy].toLowerCase().includes(searchValue.toLowerCase());
            } else {
              return account[filterBy].toString().includes(searchValue);
            }
          });
        }

        renderTable(filteredData);
      });

      filterOptionsEl.addEventListener("change", () => {
        searchEl.value = "";
        renderTable(accounts);
      });
    }

    // Hàm render bảng
    function renderTable(displayedAccounts) {
      const tableBody = document.getElementById('table-body');
      const noProductsEl = document.querySelector('.no-products');

      if (!tableBody || !noProductsEl) {
        console.error('Required elements not found: #table-body or .no-products');
        return;
      }

      tableBody.innerHTML = '';
      const activeAccounts = displayedAccounts.filter(account => account.status_id !== "6");

      if (activeAccounts.length > 0) {
        noProductsEl.style.display = 'none';
        activeAccounts.forEach((account, index) => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${index + 1}</td>
            <td>${account.account_name || 'N/A'}</td>
            <td>${account.email || 'N/A'}</td>
            <td>${account.full_name || 'N/A'}</td>
            <td>${account.role_name}</td>
            <td>${getStatusText(account.account_status_id)}</td>
            <td class="actions">
              <div class="dropdown">
                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                <div class="dropdown-content">
                  <a href="#" class="viewAccount" data-permission-id="3" data-action="Xem" data-account-id="${account.account_id}">Xem <i class="fa fa-eye"></i></a>
                  <a href="#" class="editAccount" data-permission-id="3" data-action="Sửa" data-account-id="${account.account_id}">Sửa <i class="fa fa-edit"></i></a>
                  <a href="#" class="deleteAccount" data-permission-id="3" data-action="Xóa" data-account-id="${account.account_id}">Xóa <i class="fa fa-trash"></i></a>
                </div>
              </div>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        noProductsEl.style.display = 'flex';
        tableBody.innerHTML = '<tr><td colspan="7">Không tìm thấy tài khoản nào.</td></tr>';
      }
    }

    // Fetch dữ liệu ban đầu từ server
    fetch('quanlitaikhoan/fetch_taikhoan.php')
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          accounts = data.data;
          console.log('Initial accounts:', accounts);
          renderTable(accounts);
          addFilterEventListener();
        } else {
          console.error('Error:', data.message);
          document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Lỗi khi tải danh sách tài khoản.</td></tr>';
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('table-body').innerHTML = '<tr><td colspan="7">Lỗi khi tải danh sách tài khoản.</td></tr>';
      });

    // Sử dụng event delegation để xử lý các hành động
    document.getElementById('table-body').addEventListener('click', (e) => {
      const target = e.target.closest('a');
      if (!target) return;

      e.preventDefault();
      const accountId = target.getAttribute('data-account-id');
      const account = accounts.find(acc => acc.account_id === accountId);

      if (!account) {
        console.error('Account not found:', accountId);
        return;
      }

      if (target.classList.contains('viewAccount')) {
        const viewModalEl = document.getElementById("view-modal");
        addModalData(viewModalEl, account, "innerHTML");
        viewModalEl.showModal();
      } else if (target.classList.contains('editAccount')) {
        const editModalEl = document.getElementById("edit-modal");
        openEditModal(account);
      } else if (target.classList.contains('deleteAccount')) {
        const deleteModalEl = document.getElementById("delete-modal");
        deleteModalEl.setAttribute("data-account-id", accountId);
        deleteModalEl.showModal();
      }
    });

    // Hàm mở modal chỉnh sửa
    function openEditModal(account) {
      console.log('account.status_id:', account.status_id, 'type:', typeof account.status_id);
      const editModal = document.getElementById('edit-modal');
      const form = document.getElementById('modal-edit-form');

      document.getElementById('modal-edit-account-id').value = account.account_id;
      document.getElementById('modal-edit-account-name').value = account.account_name || '';
      document.getElementById('modal-edit-email').value = account.email || '';
      document.getElementById('modal-edit-full-name').value = account.full_name || '';
      document.getElementById('modal-edit-role-id').value = account.role_id;
      document.getElementById('modal-edit-status').value = account.account_status_id;
      document.getElementById('modal-edit-date-of-birth').value = account.date_of_birth || '';

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

      updateAccount(form);
    }

    // Hàm cập nhật tài khoản
    function updateAccount(form) {
      const formData = new FormData(form);
      fetch('../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php', {
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
          fetch('quanlitaikhoan/fetch_taikhoan.php')
            .then(response => response.json())
            .then(data => {
              accounts = data.data;
              renderTable(accounts);
              editModal.close();
              const successMessage = document.getElementById('success-message');
              successMessage.querySelector('.success-text p').textContent = result.message || 'Tài khoản đã được cập nhật';
              successMessage.style.display = 'block';
              setTimeout(() => {
                successMessage.style.display = 'none';
              }, 3000);
            })
            .catch(error => console.error('Có lỗi khi lấy dữ liệu tài khoản:', error));
        } else {
          const errorContainer = editModal.querySelector('.modal-error');
          errorContainer.textContent = result.message || 'Có lỗi khi cập nhật tài khoản';
          errorContainer.style.display = 'block';
          errorContainer.style.color = 'var(--clr-error)';
          editModal.scrollTop = 0;
        }
      })
      .catch(error => {
        console.error('Cập nhật tài khoản thất bại:', error);
        const editModal = document.getElementById('edit-modal');
        editModal.scrollTop = 0;
      });
    }

    // Hàm thêm tài khoản
    function addAccount(formEl) {
      const formData = new FormData(formEl);
      fetch('../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php', {
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
        const addAccountModal = document.getElementById("add-modal");
        if (result.status === 'success') {
          fetch('quanlitaikhoan/fetch_taikhoan.php')
            .then(response => response.json())
            .then(data => {
              accounts = data.data;
              renderTable(accounts);
              addAccountModal.close();
              const successMessage = document.getElementById('success-message');
              successMessage.querySelector('.success-text p').textContent = result.message || 'Tài khoản thêm thành công';
              successMessage.style.display = 'block';
              setTimeout(() => {
                successMessage.style.display = 'none';
              }, 3000);
            })
            .catch(error => console.error('Có lỗi khi lấy dữ liệu tài khoản:', error));
        } else {
          const errorContainer = addAccountModal.querySelector('.modal-error');
          errorContainer.textContent = result.message || 'Có lỗi khi thêm tài khoản';
          errorContainer.style.display = 'block';
          errorContainer.style.color = 'var(--clr-error)';
          addAccountModal.scrollTop = 0;
        }
      })
      .catch(error => {
        console.error('Thêm tài khoản thất bại:', error);
        const addAccountModal = document.getElementById("add-modal");
        addAccountModal.scrollTop = 0;
      });
    }

    // Hàm xóa tài khoản (cập nhật status_id thành 6)
    function deleteAccount(accountId) {
      const formData = new FormData();
      formData.append('account_id', accountId);
      formData.append('status_id', "6");

      fetch('../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(result => {
        if (result.status === 'success') {
          fetch('quanlitaikhoan/fetch_taikhoan.php')
            .then(response => response.json())
            .then(data => {
              accounts = data.data;
              renderTable(accounts);
              const deleteModalEl = document.getElementById('delete-modal');
              deleteModalEl.close();
              const successMessage = document.getElementById('success-message');
              successMessage.querySelector('.success-text p').textContent = result.message || 'Tài khoản đã được đánh dấu xóa';
              successMessage.style.display = 'block';
              setTimeout(() => {
                successMessage.style.display = 'none';
              }, 3000);
            })
            .catch(error => console.error('Có lỗi khi lấy dữ liệu tài khoản:', error));
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
      const accountId = parseInt(deleteModalEl.getAttribute('data-account-id'));
      deleteAccount(accountId);
    });

    // Hàm xử lý modal Add
    function addViewAccountModalEventListener() {
      const addAccountModal = document.getElementById("add-modal");
      const formEl = document.getElementById("modal-add-form");
      const addCloseButton = addAccountModal.querySelector("#add-close-button");
      const addAccountToolbar = document.querySelector("#add-product-toolbar");

      addAccountToolbar.addEventListener("click", () => {
        addAccountModal.showModal();
      });

      addCloseButton.addEventListener("click", () => {
        addAccountModal.close();
      });

      formEl.addEventListener("submit", (e) => {
        e.preventDefault();
        const isError = validateAddModalFormInputs(formEl);
        if (!isError) {
          addAccount(formEl);
        } else {
          addAccountModal.scrollTop = 0;
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

        if (input.id === 'modal-edit-account-name') {
          if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Tên tài khoản chỉ chứa chữ cái, số, và dấu gạch dưới';
          } else if (value.length > 50) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Tên tài khoản không được vượt quá 50 ký tự';
          }
        }

        if (input.id === 'modal-edit-email') {
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Email không hợp lệ';
          }
        }

        if (input.id === 'modal-edit-full-name') {
          if (!/^[a-zA-Z\s-]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
          } else if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên không được vượt quá 100 ký tự';
          }
        }

        if (input.id === 'modal-edit-date-of-birth') {
          const dob = new Date(value);
          const today = new Date();
          if (dob >= today) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Ngày sinh phải trước ngày hiện tại';
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

        if (input.id === 'modal-add-account-name') {
          if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Tên tài khoản chỉ chứa chữ cái, số, và dấu gạch dưới';
          } else if (value.length > 50) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Tên tài khoản không được vượt quá 50 ký tự';
          }
        }

        if (input.id === 'modal-add-email') {
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Email không hợp lệ';
          }
        }

        if (input.id === 'modal-add-full-name') {
          if (!/^[a-zA-Z\s-]+$/.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
          } else if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên không được vượt quá 100 ký tự';
          }
        }

        if (input.id === 'modal-add-date-of-birth') {
          const dob = new Date(value);
          const today = new Date();
          if (dob >= today) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Ngày sinh phải trước ngày hiện tại';
          }
        }

        if (input.id === 'modal-add-password') {
          if (value.length < 6) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Mật khẩu phải có ít nhất 6 ký tự';
          }
        }
      });

      return isError;
    }

    // Hàm thêm dữ liệu vào modal
    function addModalData(modalEl, account, type) {
      if (type === "innerHTML") {
        modalEl.querySelector("#modal-view-account-id").textContent = account.account_id || 'N/A';
        modalEl.querySelector("#modal-view-account-name").textContent = account.account_name || 'N/A';
        modalEl.querySelector("#modal-view-email").textContent = account.email || 'N/A';
        modalEl.querySelector("#modal-view-full-name").textContent = account.full_name || 'N/A';
        modalEl.querySelector("#modal-view-role-id").textContent = account.role_name;
        modalEl.querySelector("#modal-view-status-id").textContent = getStatusText(account.account_status_id);
        modalEl.querySelector("#modal-view-date-of-birth").textContent = account.date_of_birth || 'N/A';
        const profilePicture = account.profile_picture ? `/Web2/BackEnd/Uploads/Profile Picture/${account.profile_picture}` : 'default.jpg';
        modalEl.querySelector("#modal-view-profile-picture").src = profilePicture;

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
    addViewAccountModalEventListener();
    addModalCloseButtonEventListeners();
    const addModal = document.getElementById('add-modal');
    if (addModal) addModalCancelButtonEventListener(addModal);
    const editModal = document.getElementById('edit-modal');
    if (editModal) addModalCancelButtonEventListener(editModal);
    const viewModal = document.getElementById('view-modal');
    if (viewModal) addModalCancelButtonEventListener(viewModal);
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) addModalCancelButtonEventListener(deleteModal);
  });
  </script>
</div>

<?php
include 'quanlitaikhoan/themtaikhoan.php'; // Modal thêm tài khoản
include 'quanlitaikhoan/suataikhoan.php';  // Modal sửa tài khoản
include 'quanlitaikhoan/xemtaikhoan.php';  // Modal xem tài khoản
include 'quanlitaikhoan/xoataikhoan.php';  // Modal xóa tài khoản
?>