<div class="header"></div>
<div class="data-table">
  <div class="success-message" id="success-message" style="display: none">
    <div class="success-text">
      <p>Văn bản mẫu</p>
      <a id="success-message-cross" style="cursor: pointer">
        <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
      </a>
    </div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
  </div>
  <h1 class="heading"> Quản lý <span>TÀI KHOẢN</span></h1>
  <div class="toolbar">
    <div class="filters">
      <div class="filter-options-wrapper">
        <label for="filter-options" class="filter-label">Bộ lọc </label>
        <select id="filter-options">
          <option value="account_name">Tên tài khoản</option>
          <option value="email">Email</option>
          <option value="full_name">Họ tên</option>
          <option value="address">Địa chỉ</option>
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
          <th data-id="address">Địa chỉ</th>
          <th data-id="role_id">Chức vụ</th>
          <th data-id="status_id">Trạng thái</th>
          <th class="actionsTH">Hành động</th>
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
    let roles = []; // Danh sách chức vụ

    // Hàm chuyển status_id thành văn bản
    function getStatusText(statusId) {
      switch (statusId) {
        case "1": return 'Hoạt động';
        case "2": return 'Không hoạt động';
        default: return 'N/A';
      }
    }

    // Hàm điền danh sách chức vụ vào dropdown
    function populateRoleDropdown() {
      const addRoleSelect = document.getElementById('modal-add-role-id');
      const editRoleSelect = document.getElementById('modal-edit-role-id');

      if (addRoleSelect) {
        addRoleSelect.innerHTML = '<option value="">Chọn chức vụ</option>';
        roles.forEach(role => {
          const option = document.createElement('option');
          option.value = role.id;
          option.textContent = role.role_name;
          addRoleSelect.appendChild(option);
        });
      }

      if (editRoleSelect) {
        editRoleSelect.innerHTML = '<option value="">Chọn chức vụ</option>';
        roles.forEach(role => {
          const option = document.createElement('option');
          option.value = role.id;
          option.textContent = role.role_name;
          editRoleSelect.appendChild(option);
        });
      }
    }

    // Hàm thêm sự kiện lọc và tìm kiếm
    function addFilterEventListener() {
      const searchEl = document.getElementById("search-text");
      const filterOptionsEl = document.getElementById("filter-options");

      if (!searchEl || !filterOptionsEl) {
        console.error('Không tìm thấy các phần tử cần thiết: #search-text hoặc #filter-options');
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
        console.error('Không tìm thấy các phần tử cần thiết: #table-body hoặc .no-products');
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
            <td>${account.address || 'N/A'}</td>
            <td>${account.role_name}</td>
            <td>${getStatusText(account.account_status_id)}</td>
            <td class="actions">
              <div class="dropdown">
                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                <div class="dropdown-content">
                  <a href="#" class="viewAccount" data-permission-id="3" data-action="Xem" data-account-id="${account.account_id}">Xem tài khoản <i class="fa fa-eye"></i></a>
                  <a href="#" class="editAccount" data-permission-id="3" data-action="Sửa" data-account-id="${account.account_id}">Sửa tài khoản <i class="fa fa-edit"></i></a>
                  <a href="#" class="deleteAccount" data-permission-id="3" data-action="Xóa" data-account-id="${account.account_id}">Xóa tài khoản <i class="fa fa-trash"></i></a>
                </div>
              </div>
            </td>
          `;
          tableBody.appendChild(row);
        });
      } else {
        noProductsEl.style.display = 'flex';
        tableBody.innerHTML = '<tr><td colspan="8">Không tìm thấy tài khoản nào.</td></tr>';
      }
    }

    // Fetch danh sách chức vụ từ server
    fetch('quanlichucvu/fetch_chucvu.php')
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          roles = data.data;
          console.log('Danh sách chức vụ ban đầu:', roles);
          populateRoleDropdown();
        } else {
          console.error('Lỗi khi lấy danh sách chức vụ:', data.message);
        }
      })
      .catch(error => {
        console.error('Lỗi tải dữ liệu chức vụ:', error);
      });

    // Fetch dữ liệu tài khoản ban đầu từ server
    fetch('quanlitaikhoan/fetch_taikhoan.php')
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          accounts = data.data;
          console.log('Danh sách tài khoản ban đầu:', accounts);
          renderTable(accounts);
          addFilterEventListener();
        } else {
          console.error('Lỗi:', data.message);
          document.getElementById('table-body').innerHTML = '<tr><td colspan="8">Lỗi khi tải danh sách tài khoản.</td></tr>';
        }
      })
      .catch(error => {
        console.error('Lỗi tải dữ liệu:', error);
        document.getElementById('table-body').innerHTML = '<tr><td colspan="8">Lỗi khi tải danh sách tài khoản.</td></tr>';
      });

    // Sử dụng event delegation để xử lý các hành động
    document.getElementById('table-body').addEventListener('click', (e) => {
      const target = e.target.closest('a');
      if (!target) return;

      e.preventDefault();
      const accountId = target.getAttribute('data-account-id');
      const account = accounts.find(acc => acc.account_id === accountId);

      if (!account) {
        console.error('Không tìm thấy tài khoản:', accountId);
        return;
      }

      if (target.classList.contains('viewAccount')) {
        const viewModalEl = document.getElementById("view-modal");
        if (viewModalEl) {
          addModalData(viewModalEl, account, "innerHTML");
          viewModalEl.showModal();
        } else {
          console.error('Modal xem với id "view-modal" không được tìm thấy trong DOM!');
        }
      } else if (target.classList.contains('editAccount')) {
        const editModalEl = document.getElementById("edit-modal");
        if (editModalEl) {
          openEditModal(account);
        } else {
          console.error('Modal sửa với id "edit-modal" không được tìm thấy trong DOM!');
        }
      } else if (target.classList.contains('deleteAccount')) {
        const deleteModalEl = document.getElementById("delete-modal");
        if (deleteModalEl) {
          deleteModalEl.setAttribute("data-account-id", accountId);
          deleteModalEl.showModal();
        } else {
          console.error('Modal xóa với id "delete-modal" không được tìm thấy trong DOM!');
        }
      }
    });

    // Hàm mở modal chỉnh sửa
    function openEditModal(account) {
      const editModal = document.getElementById('edit-modal');
      const form = document.getElementById('modal-edit-form');

      if (!editModal || !form) {
        console.error('Modal sửa hoặc form không được tìm thấy!');
        return;
      }

      document.getElementById('modal-edit-account-id').value = account.account_id;
      document.getElementById('modal-edit-account-name').value = account.account_name || '';
      document.getElementById('modal-edit-email').value = account.email || '';
      document.getElementById('modal-edit-full-name').value = account.full_name || '';
      document.getElementById('modal-edit-address').value = account.address || '';
      document.getElementById('modal-edit-role-id').value = account.role_id;
      document.getElementById('modal-edit-status').value = account.account_status_id;
      document.getElementById('modal-edit-date-of-birth').value = account.date_of_birth || '';

      // Hiển thị ảnh hiện tại (nếu có)
      const previewImg = document.getElementById('modal-edit-profile-picture-preview');
      const profilePicture = account.profile_picture ? `/Web2/BackEnd/Uploads/Profile Picture/${account.profile_picture}` : '';
      if (previewImg) {
        if (profilePicture) {
          previewImg.src = profilePicture;
          previewImg.style.display = 'block';
        } else {
          previewImg.src = '';
          previewImg.style.display = 'none';
        }
      }

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
      // Lấy role_id từ form và chuyển thành chuỗi
      const roleId = formData.get('role_id');
      if (roleId !== null) {
        formData.set('role_id', String(roleId)); // Chuyển role_id thành chuỗi
      }

      fetch('../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Phản hồi mạng không ổn');
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
      // Lấy role_id từ form và chuyển thành chuỗi
      const roleId = formData.get('role_id');
      if (roleId !== null) {
        formData.set('role_id', String(roleId)); // Chuyển role_id thành chuỗi
      }

      fetch('../../BackEnd/Model/quanlitaikhoan/xulitaikhoan.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Phản hồi mạng không ổn');
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
              // Reset form trước khi đóng modal
              clearFormErrors(formEl);
              resetImagePreview('add-modal');
              formEl.reset(); // Reset tất cả các trường trong form
              addAccountModal.close();
              const successMessage = document.getElementById('success-message');
              successMessage.querySelector('.success-text p').textContent = result.message || 'Thêm tài khoản thành công';
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
      // Kiểm tra nếu account_id là 1
      if (parseInt(accountId) === 1) {
        const successMessage = document.getElementById('success-message');
        successMessage.querySelector('.success-text p').textContent = 'Không thể xóa tài khoản admin hệ thống!';
        successMessage.style.display = 'block';
        successMessage.style.backgroundColor = 'var(--clr-error)';
        setTimeout(() => {
          successMessage.style.display = 'none';
          successMessage.style.backgroundColor = '';
        }, 3000);
        const deleteModalEl = document.getElementById('delete-modal');
        if (deleteModalEl) {
          deleteModalEl.close();
        }
        return;
      }

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
              if (deleteModalEl) {
                deleteModalEl.close();
              }
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
    if (deleteModalEl) {
      const deleteDeleteButton = deleteModalEl.querySelector('#delete-delete-button');
      if (deleteDeleteButton) {
        deleteDeleteButton.addEventListener('click', () => {
          const accountId = parseInt(deleteModalEl.getAttribute('data-account-id'));
          deleteAccount(accountId);
        });
      } else {
        console.error('Nút xóa với id "delete-delete-button" không được tìm thấy trong modal xóa!');
      }
    } else {
      console.error('Modal xóa với id "delete-modal" không được tìm thấy trong DOM!');
    }

    // Hàm xử lý modal Add
    function addViewAccountModalEventListener() {
      const addAccountModal = document.getElementById("add-modal");
      const formEl = document.getElementById("modal-add-form");
      const addCloseButton = addAccountModal.querySelector("#add-close-button");
      const addAccountToolbar = document.querySelector("#add-product-toolbar");

      if (!addAccountModal || !formEl || !addCloseButton || !addAccountToolbar) {
        console.error('Không tìm thấy các phần tử cần thiết cho modal thêm!');
        return;
      }

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
            if (errorEl) errorEl.textContent = 'Tên tài khoản chỉ chứa chữ cái, số và dấu gạch dưới';
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
          if (!/^[\p{L}\s]+$/u.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên chỉ chứa chữ cái tiếng Việt và khoảng trắng (VD: Nguyễn Văn A)';
          } else if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên không được vượt quá 100 ký tự';
          }
        }

        if (input.id === 'modal-edit-address') {
          if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Địa chỉ không được vượt quá 100 ký tự';
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

        if (input.id === 'modal-edit-role-id') {
          if (!roles.some(role => String(role.id) === String(value))) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Vui lòng chọn một chức vụ hợp lệ!';
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
            if (errorEl) errorEl.textContent = 'Tên tài khoản chỉ chứa chữ cái, số và dấu gạch dưới';
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
          if (!/^[\p{L}\s]+$/u.test(value)) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên chỉ chứa chữ cái tiếng Việt và khoảng trắng (VD: Nguyễn Văn A)';
          } else if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Họ tên không được vượt quá 100 ký tự';
          }
        }

        if (input.id === 'modal-add-address') {
          if (value.length > 100) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Địa chỉ không được vượt quá 100 ký tự';
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

        if (input.id === 'modal-add-role-id') {
          if (!roles.some(role => String(role.id) === String(value))) {
            isError = true;
            input.style.border = '1px solid var(--clr-error)';
            if (errorEl) errorEl.textContent = 'Vui lòng chọn một chức vụ hợp lệ!';
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
        modalEl.querySelector("#modal-view-address").textContent = account.address || 'N/A';
        modalEl.querySelector("#modal-view-role-id").textContent = account.role_name;
        modalEl.querySelector("#modal-view-status-id").textContent = getStatusText(account.account_status_id);
        modalEl.querySelector("#modal-view-date-of-birth").textContent = account.date_of_birth || 'N/A';
        const profilePicture = account.profile_picture ? `/Web2/BackEnd/Uploads/Profile Picture/${account.profile_picture}` : 'default.jpg';
        modalEl.querySelector("#modal-view-profile-picture").src = profilePicture;
        modalEl.querySelector("#modal-view-created-at").textContent = account.account_created_at || 'N/A';
        modalEl.querySelector("#modal-view-updated-at").textContent = account.account_updated_at || 'N/A';
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
              resetImagePreview(modalId);
            }
          }
        }
      });
    }

    function addModalCancelButtonEventListener(modalEl) {
      const cancelButton = modalEl.querySelector('[id$="-close-button"]');
      if (!cancelButton) {
        console.error('Nút hủy với id kết thúc bằng "-close-button" không được tìm thấy trong modal!');
        return;
      }

      cancelButton.addEventListener("click", () => {
        modalEl.close();
        const formEl = modalEl.querySelector('form.modal-form');
        if (formEl) {
          clearFormErrors(formEl);
          resetImagePreview(modalEl.id);
        }
      });
    }

    // Hàm reset ảnh preview
    function resetImagePreview(modalId) {
      let previewImg;
      if (modalId === 'add-modal') {
        previewImg = document.getElementById('modal-add-profile-picture-preview');
      } else if (modalId === 'edit-modal') {
        previewImg = document.getElementById('modal-edit-profile-picture-preview');
      }

      if (previewImg) {
        previewImg.src = '';
        previewImg.style.display = 'none';
      }

      // Reset input file
      const fileInput = modalId === 'add-modal' 
        ? document.getElementById('modal-add-profile-picture')
        : document.getElementById('modal-edit-profile-picture');
      if (fileInput) {
        fileInput.value = ''; // Xóa giá trị input file
      }
    }

    // Thêm sự kiện preview ảnh
    function addImagePreviewListeners() {
      // Preview ảnh cho modal thêm
      const addFileInput = document.getElementById('modal-add-profile-picture');
      const addPreviewImg = document.getElementById('modal-add-profile-picture-preview');

      if (addFileInput && addPreviewImg) {
        addFileInput.addEventListener('change', (e) => {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
              addPreviewImg.src = event.target.result;
              addPreviewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            addPreviewImg.src = '';
            addPreviewImg.style.display = 'none';
          }
        });
      }

      // Preview ảnh cho modal sửa
      const editFileInput = document.getElementById('modal-edit-profile-picture');
      const editPreviewImg = document.getElementById('modal-edit-profile-picture-preview');

      if (editFileInput && editPreviewImg) {
        editFileInput.addEventListener('change', (e) => {
          const file = e.target.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
              editPreviewImg.src = event.target.result;
              editPreviewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            editPreviewImg.src = '';
            editPreviewImg.style.display = 'none';
          }
        });
      }
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

    // Thêm sự kiện preview ảnh
    addImagePreviewListeners();
  });
  </script>
</div>

<?php
include 'quanlitaikhoan/themtaikhoan.php'; // Modal thêm tài khoản
include 'quanlitaikhoan/suataikhoan.php';  // Modal sửa tài khoản
include 'quanlitaikhoan/xemtaikhoan.php';  // Modal xem tài khoản
include 'quanlitaikhoan/xoataikhoan.php';  // Modal xóa tài khoản
?>