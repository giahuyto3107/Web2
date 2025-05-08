<body>
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
    <h1 class="heading"> Quản lý <span>CHỨC VỤ</span></h1>
    <div class="toolbar">
        <div class="filters">
            <div class="filter-options-wrapper">
                <label for="filter-options" class="filter-label">Bộ lọc </label>
                <select id="filter-options">
                    <option value="role_name">Tên chức vụ</option>
                    <option value="role_description">Mô tả</option>
                    <option value="status_id">Trạng thái</option>
                </select>
            </div>
            <div class="search">
                <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
            </div>
        </div>
        <div class="toolbar-button-wrapper">
            <button class="toolbar-button add-product-button" id="add-product-toolbar" data-permission-id="10" data-action="Thêm">
                <span>Thêm chức vụ</span>
                <i class="bx bx-plus-medical"></i>
            </button>
        </div>
    </div>

    <div id="selected-products"></div>

    <div class="table-container">
        <div class="no-products">
            <p>Có vẻ hiện tại bạn chưa có chức vụ nào?</p>
        </div>

        <table class="table" id="data-table">
            <thead>
                <tr>
                    <th data-id="id">ID</th>
                    <th data-id="role_name">Tên chức vụ</th>
                    <th data-id="role_description">Mô tả</th>
                    <th data-id="status_id">Trạng thái</th>
                    <th class="actionsTH">Quản lý</th>
                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>
    </div>

    <!-- Script để fetch và hiển thị dữ liệu -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biến toàn cục
        let roles = []; // Dữ liệu gốc, không thay đổi
        let currentUserRoleId = null; // Role ID của người dùng hiện tại

        // Lấy role_id của người dùng hiện tại từ server
        fetch('../../BackEnd/Model/quanlichucvu/get_current_user_role.php')
            .then(response => response.json())
            .then(userData => {
                if (userData.status === 'success') {
                    currentUserRoleId = parseInt(userData.role_id);
                    console.log('Current User Role ID:', currentUserRoleId);
                } else {
                    console.error('Error getting current user role:', userData.message);
                    currentUserRoleId = 0; // Giá trị mặc định nếu không lấy được
                }
            })
            .catch(error => {
                console.error('Error fetching current user role:', error);
                currentUserRoleId = 0;
            });

        // Hàm kiểm tra quyền admin chỉ cho role_id = 1 hoặc 2
        function checkAdminPermission(targetRoleId, action) {
            if ([1, 2].includes(parseInt(targetRoleId))) {
                const isAdminRole = currentUserRoleId === 1;
                if (!isAdminRole) {
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = 'Chỉ admin (role_id = 1) mới có quyền thực hiện hành động này!';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = '';
                    }, 3000);
                    return false;
                }
            }
            return true;
        }

       // Hàm kiểm tra quyền sửa và cập nhật quyền dựa trên role_id
       function checkRolePermission(targetRoleId, action) {
            // Cấm chỉnh sửa role_id 1 hoặc 2 nếu không phải admin
            if ([1, 2].includes(parseInt(targetRoleId)) && currentUserRoleId !== 1) {
                const successMessage = document.getElementById('success-message');
                successMessage.querySelector('.success-text p').textContent = `Chỉ admin (role_id = 1) mới có thể ${action} chức vụ role_id 1 hoặc 2!`;
                successMessage.style.display = 'block';
                successMessage.style.backgroundColor = 'var(--clr-error)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    successMessage.style.backgroundColor = '';
                }, 3000);
                return false;
            }

            // Cấm mọi role_id cập nhật quyền của chính mình, ngoại lệ admin (role_id = 1)
            if (currentUserRoleId === parseInt(targetRoleId) && action === 'cập nhật phân quyền' && currentUserRoleId !== 1) {
                const successMessage = document.getElementById('success-message');
                successMessage.querySelector('.success-text p').textContent = `Tài khoản role_id ${currentUserRoleId} không thể cập nhật quyền của chính mình!`;
                successMessage.style.display = 'block';
                successMessage.style.backgroundColor = 'var(--clr-error)';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    successMessage.style.backgroundColor = '';
                }, 3000);
                return false;
            }

            // Nếu targetRoleId không phải 1 hoặc 2, cho phép chỉnh sửa
            return true;
        }

        // Hàm chuyển status_id thành văn bản
        function getStatusText(statusId) {
            switch (parseInt(statusId)) {
                case 1: return 'Hoạt động';
                case 2: return 'Không hoạt động';
                case 6: return 'Deleted';
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

                let filteredData = roles;

                if (searchValue !== "") {
                    filteredData = roles.filter((role) => {
                        if (typeof role[filterBy] === "string") {
                            return role[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                        } else {
                            return role[filterBy].toString().includes(searchValue);
                        }
                    });
                }

                renderTable(filteredData);
            });

            filterOptionsEl.addEventListener("change", () => {
                searchEl.value = "";
                renderTable(roles);
            });
        }

        // Hàm render bảng
        function renderTable(displayedRoles) {
            const tableBody = document.getElementById('table-body');
            const noProductsEl = document.querySelector('.no-products');

            if (!tableBody || !noProductsEl) {
                console.error('Required elements not found: #table-body or .no-products');
                return;
            }

            tableBody.innerHTML = '';
            const activeRoles = displayedRoles.filter(role => parseInt(role.status_id) !== 6);

            if (activeRoles.length > 0) {
                noProductsEl.style.display = 'none';
                activeRoles.forEach((role, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${role.id}</td>
                        <td>${role.role_name || 'N/A'}</td>
                        <td>${role.role_description || 'N/A'}</td>
                        <td>${getStatusText(role.status_id)}</td>
                        <td class="actions">
                            <div class="dropdown">
                                <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                <div class="dropdown-content">
                                    <a href="#" class="viewRole" data-permission-id="10" data-action="Xem" data-role-id="${role.id}">Xem <i class="fa fa-eye"></i></a>
                                    <a href="#" class="editRole" data-permission-id="10" data-action="Sửa" data-role-id="${role.id}">Sửa <i class="fa fa-edit"></i></a>
                                    <a href="#" class="deleteRole" data-permission-id="10" data-action="Xóa" data-role-id="${role.id}">Xóa <i class="fa fa-trash"></i></a>
                                    <a href="#" class="updatePermission" data-permission-id="10" data-action="Cập nhật phân quyền" data-role-id="${role.id}" data-role-name="${role.role_name}">Cập nhật quyền <i class="fa fa-key"></i></a>
                                </div>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                noProductsEl.style.display = 'flex';
                tableBody.innerHTML = '<tr><td colspan="5">Không tìm thấy chức vụ.</td></tr>';
            }
        }

        // Fetch dữ liệu ban đầu từ server
        fetch('quanlichucvu/fetch_chucvu.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    roles = data.data;
                    console.log('Initial roles:', roles);
                    renderTable(roles);
                    addFilterEventListener();
                } else {
                    console.error('Error:', data.message);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải chức vụ.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('table-body').innerHTML = '<tr><td colspan="5">Lỗi khi tải chức vụ.</td></tr>';
            });

        // Sử dụng event delegation để xử lý các hành động
        document.getElementById('table-body').addEventListener('click', (e) => {
            const target = e.target.closest('a');
            if (!target) return;

            e.preventDefault();
            const roleId = parseInt(target.getAttribute('data-role-id'));
            const role = roles.find(r => parseInt(r.id) === roleId);
            const roleName = target.getAttribute('data-role-name');

            if (!role) {
                console.error('Role not found:', roleId);
                alert('Chức vụ không tồn tại hoặc đã bị xóa. Vui lòng làm mới trang.');
                return;
            }

            if (target.classList.contains('viewRole')) {
                const viewModalEl = document.getElementById("view-modal");
                addModalData(viewModalEl, role, "innerHTML");
                viewModalEl.showModal();
            } else if (target.classList.contains('editRole')) {
                if (!checkAdminPermission(roleId, 'sửa') || !checkRolePermission(roleId, 'sửa')) return;
                const editModalEl = document.getElementById("edit-modal");
                openEditModal(role);
            } else if (target.classList.contains('deleteRole')) {
                if (!checkAdminPermission(roleId, 'xóa') || !checkRolePermission(roleId, 'xóa')) return;

                // Kiểm tra nếu role_id đang xóa trùng với role_id của người dùng
                if (roleId === currentUserRoleId) {
                    const successMessage = document.getElementById('success-message');
                    successMessage.querySelector('.success-text p').textContent = 'Bạn không thể xóa chức vụ của chính mình!';
                    successMessage.style.display = 'block';
                    successMessage.style.backgroundColor = 'var(--clr-error)';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                        successMessage.style.backgroundColor = '';
                    }, 3000);
                    return;
                }

                const deleteModalEl = document.getElementById("delete-modal");
                deleteModalEl.setAttribute("data-role-id", roleId);
                deleteModalEl.showModal();
            } else if (target.classList.contains('updatePermission')) {
                if (!checkAdminPermission(roleId, 'cập nhật phân quyền') || !checkRolePermission(roleId, 'cập nhật phân quyền')) return;
                const permissionModal = document.getElementById('permission-modal');
                document.getElementById('modal-role-id').value = roleId;
                document.getElementById('modal-role-name').textContent = roleName;
                fetchPermissions(roleId, permissionModal);
                permissionModal.showModal();
            }
        });

        function fetchPermissions(roleId, permissionModal) {
            const permissionActions = {
                "Đặt hàng": ["Đặt hàng"],
                "Quản lý hóa đơn": ["Xem", "Duyệt đơn/Hoàn tất", "Hủy"],
                "Quản lý phiếu nhập": ["Xem"],
                "Quản lý đánh giá": ["Xem", "Xóa", "Sửa"],
                "Thống kê": ["Xem"],
                "Quản lý chức vụ": ["Xem", "Thêm", "Xóa", "Sửa", "Cập nhật phân quyền"]
            };
            const defaultActions = ["Xem", "Thêm", "Xóa", "Sửa"];
            const allActions = ["Xem", "Thêm", "Xóa", "Sửa", "Cập nhật phân quyền", "Duyệt đơn/Hoàn tất", "Hủy", "Đặt hàng"];

            // Check if current user has permission to modify this role
            fetch('../../BackEnd/Model/quanlichucvu/get_current_user_role.php')
                .then(response => response.json())
                .then(userData => {
                    if (userData.status !== 'success') {
                        console.error('Error getting current user role:', userData.message);
                        alert('Không thể xác định quyền của người dùng hiện tại. Vui lòng thử lại.');
                        return;
                    }

                    const currentUserRoleId = parseInt(userData.role_id);
                    if (!checkAdminPermission(roleId, 'cập nhật phân quyền') || !checkRolePermission(roleId, 'cập nhật phân quyền')) {
                        permissionModal.close();
                        return;
                    }

                    const permissionActions = {
                        "Đặt hàng": ["Đặt hàng"],
                        "Quản lý hóa đơn": ["Xem", "Duyệt đơn/Hoàn tất", "Hủy"],
                        "Quản lý phiếu nhập": ["Xem"],
                        "Quản lý đánh giá": ["Xem", "Xóa", "Sửa"],
                        "Thống kê": ["Xem"],
                        "Quản lý chức vụ": ["Xem", "Thêm", "Xóa", "Sửa", "Cập nhật phân quyền"]
                    };
                    const defaultActions = ["Xem", "Thêm", "Xóa", "Sửa"];
                    const allActions = ["Xem", "Thêm", "Xóa", "Sửa", "Cập nhật phân quyền", "Duyệt đơn/Hoàn tất", "Hủy", "Đặt hàng"];

                    fetch('../../BackEnd/Model/quanlichucvu/fetch_quyen.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(allPermissions => {
                            if (allPermissions.status !== 'success') {
                                console.error('Lỗi khi lấy danh sách quyền:', allPermissions.message);
                                alert('Không thể lấy danh sách quyền. Vui lòng thử lại.');
                                return;
                            }

                            fetch(`quanlichucvu/fetch_chucvu_phanquyen.php?role_id=${roleId}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! Status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(assignedPermissions => {
                                    if (assignedPermissions.status !== 'success') {
                                        console.error('Lỗi khi lấy quyền đã gán:', assignedPermissions.message);
                                        alert('Không thể lấy quyền đã gán. Vui lòng thử lại.');
                                        return;
                                    }

                                    const permissionsList = document.getElementById('permissions-list');
                                    permissionsList.innerHTML = '';

                                    const assignedSet = new Set(
                                        assignedPermissions.data.map(item => `${item.permission_id}-${item.action}`)
                                    );

                                    const table = document.createElement('table');
                                    table.style.width = '100%';
                                    table.style.borderCollapse = 'collapse';

                                    const thead = document.createElement('thead');
                                    const trHead = document.createElement('tr');
                                    const thPermission = document.createElement('th');
                                    thPermission.textContent = 'Quyền';
                                    thPermission.style.padding = '8px';
                                    thPermission.style.borderBottom = '1px solid #ddd';
                                    trHead.appendChild(thPermission);

                                    allActions.forEach(action => {
                                        const th = document.createElement('th');
                                        th.textContent = action;
                                        th.style.padding = '8px';
                                        th.style.borderBottom = '1px solid #ddd';
                                        trHead.appendChild(th);
                                    });

                                    const thSelectAll = document.createElement('th');
                                    thSelectAll.textContent = 'Select All';
                                    thSelectAll.style.padding = '8px';
                                    thSelectAll.style.borderBottom = '1px solid #ddd';
                                    trHead.appendChild(thSelectAll);
                                    thead.appendChild(trHead);
                                    table.appendChild(thead);

                                    const tbody = document.createElement('tbody');

                                    allPermissions.data.forEach(permission => {
                                        const allowedActions = permissionActions[permission.name] || defaultActions;
                                        const tr = document.createElement('tr');
                                        tr.style.borderBottom = '1px solid #ddd';

                                        const tdPermission = document.createElement('td');
                                        tdPermission.textContent = permission.name;
                                        tdPermission.style.padding = '8px';
                                        tr.appendChild(tdPermission);

                                        allActions.forEach(action => {
                                            const td = document.createElement('td');
                                            td.style.padding = '8px';
                                            td.style.textAlign = 'center';
                                            if (allowedActions.includes(action)) {
                                                const checkbox = document.createElement('input');
                                                checkbox.type = 'checkbox';
                                                checkbox.name = `permissions[${permission.id}][${action}]`;
                                                checkbox.className = 'permission-checkbox';

                                                if (assignedSet.has(`${permission.id}-${action}`)) {
                                                    checkbox.checked = true;
                                                }

                                                td.appendChild(checkbox);
                                            }
                                            tr.appendChild(td);
                                        });

                                        const tdSelectAll = document.createElement('td');
                                        tdSelectAll.style.padding = '8px';
                                        tdSelectAll.style.textAlign = 'center';

                                        const buttonContainer = document.createElement('div');
                                        buttonContainer.style.display = 'flex';
                                        buttonContainer.style.justifyContent = 'center';
                                        buttonContainer.style.gap = '5px';

                                        const selectAllButton = document.createElement('button');
                                        selectAllButton.type = 'button';
                                        selectAllButton.textContent = 'Select All';
                                        selectAllButton.style.padding = '4px 8px';
                                        selectAllButton.style.cursor = 'pointer';
                                        selectAllButton.addEventListener('click', () => {
                                            const checkboxes = tr.querySelectorAll('input[type="checkbox"]:not(:disabled)');
                                            checkboxes.forEach(cb => cb.checked = true);
                                        });
                                        buttonContainer.appendChild(selectAllButton);

                                        const uncheckAllButton = document.createElement('button');
                                        uncheckAllButton.type = 'button';
                                        uncheckAllButton.textContent = 'Uncheck All';
                                        uncheckAllButton.style.padding = '4px 8px';
                                        uncheckAllButton.style.cursor = 'pointer';
                                        uncheckAllButton.addEventListener('click', () => {
                                            const checkboxes = tr.querySelectorAll('input[type="checkbox"]:not(:disabled)');
                                            checkboxes.forEach(cb => cb.checked = false);
                                        });
                                        buttonContainer.appendChild(uncheckAllButton);

                                        tdSelectAll.appendChild(buttonContainer);
                                        tr.appendChild(tdSelectAll);

                                        tbody.appendChild(tr);
                                    });

                                    table.appendChild(tbody);
                                    permissionsList.appendChild(table);
                                })
                                .catch(error => {
                                    console.error('Lỗi khi lấy quyền đã gán:', error);
                                    alert('Có lỗi khi lấy quyền đã gán. Vui lòng kiểm tra console để biết thêm chi tiết.');
                                });
                        })
                        .catch(error => {
                            console.error('Lỗi khi lấy danh sách quyền:', error);
                            alert('Có lỗi khi lấy danh sách quyền. Vui lòng kiểm tra console để biết thêm chi tiết.');
                        });
                })
                .catch(error => {
                    console.error('Error getting current user role:', error);
                    alert('Có lỗi khi xác định quyền của người dùng hiện tại. Vui lòng thử lại.');
                });
        }

        // Hàm updatePermission để lưu các quyền đã chọn
        function updatePermission(form) {
            const formData = new FormData(form);
            const roleId = parseInt(formData.get('role_id'));

            if (!checkAdminPermission(roleId, 'cập nhật phân quyền') || !checkRolePermission(roleId, 'cập nhật phân quyền')) {
                return;
            }

            fetch('../../BackEnd/Model/quanlichucvu/get_current_user_role.php')
                .then(response => response.json())
                .then(userData => {
                    if (userData.status !== 'success') {
                        console.error('Error getting current user role:', userData.message);
                        alert('Không thể xác định quyền của người dùng hiện tại. Vui lòng thử lại.');
                        return;
                    }

                    const currentUserRoleId = parseInt(userData.role_id);
                    const isAdminRole = currentUserRoleId === 1;
                    const isModifyingOwnRole = currentUserRoleId === roleId;

                    const hasPermissionToModify = window.PermissionSystem && 
                        window.PermissionSystem.hasActionPermission ? 
                        window.PermissionSystem.hasActionPermission(10, "Cập nhật phân quyền") : false;

                    const canModify = (isAdminRole && isModifyingOwnRole) || 
                                     (isAdminRole && !isModifyingOwnRole) || 
                                     (!isAdminRole && hasPermissionToModify && roleId !== 1);

                    if (!canModify) {
                        console.error('User does not have permission to modify this role');
                        alert('Bạn không có quyền sửa đổi quyền của chức vụ này.');
                        return;
                    }

                    fetch('../../BackEnd/Model/quanlichucvu/xulichucvu_phanquyen.php', {
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
                        const permissionModal = document.getElementById('permission-modal');
                        if (result.status === 'success') {
                            permissionModal.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Phân quyền thành công';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        } else {
                            const errorContainer = permissionModal.querySelector('.modal-error') || document.createElement('p');
                            errorContainer.classList.add('modal-error');
                            errorContainer.textContent = result.message || 'Có lỗi khi phân quyền';
                            errorContainer.style.display = 'block';
                            errorContainer.style.color = 'var(--clr-error)';
                            permissionModal.querySelector('.modal-content').insertAdjacentElement('afterbegin', errorContainer);
                            permissionModal.scrollTop = 0;
                        }
                    })
                    .catch(error => {
                        console.error('Phân quyền thất bại:', error);
                        const permissionModal = document.getElementById('permission-modal');
                        const errorContainer = permissionModal.querySelector('.modal-error') || document.createElement('p');
                        errorContainer.classList.add('modal-error');
                        errorContainer.textContent = 'Có lỗi khi phân quyền';
                        errorContainer.style.display = 'block';
                        errorContainer.style.color = 'var(--clr-error)';
                        permissionModal.querySelector('.modal-content').insertAdjacentElement('afterbegin', errorContainer);
                        permissionModal.scrollTop = 0;
                    });
                })
                .catch(error => {
                    console.error('Error getting current user role:', error);
                    alert('Có lỗi khi xác định quyền của người dùng hiện tại. Vui lòng thử lại.');
                });
        }

        // Hàm mở modal chỉnh sửa
        function openEditModal(role) {
            const editModal = document.getElementById('edit-modal');
            const form = document.getElementById('modal-edit-form');

            document.getElementById('modal-edit-role-id').value = role.id;
            document.getElementById('modal-edit-name').value = role.role_name || '';
            document.getElementById('modal-edit-description').value = role.role_description || '';
            document.getElementById('modal-edit-status').value = role.status_id;

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

            updateRole(form);
        }

        // Hàm cập nhật chức vụ
        function updateRole(form) {
            const formData = new FormData(form);
            fetch('../../BackEnd/Model/quanlichucvu/xulichucvu.php', {
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
                    fetch('quanlichucvu/fetch_chucvu.php')
                        .then(response => response.json())
                        .then(data => {
                            roles = data.data;
                            renderTable(roles);
                            editModal.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Chức vụ đã được cập nhật';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu chức vụ:', error));
                } else {
                    const errorContainer = editModal.querySelector('.modal-error');
                    errorContainer.textContent = result.message || 'Có lỗi khi cập nhật chức vụ';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    editModal.scrollTop = 0;
                }
            })
            .catch(error => {
                console.error('Cập nhật chức vụ thất bại:', error);
                const editModal = document.getElementById('edit-modal');
                editModal.scrollTop = 0;
            });
        }

        // Hàm thêm chức vụ
        function addRole(formEl) {
            const formData = new FormData(formEl);
            fetch('../../BackEnd/Model/quanlichucvu/xulichucvu.php', {
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
                const addRoleModal = document.getElementById("add-modal");
                if (result.status === 'success') {
                    fetch('quanlichucvu/fetch_chucvu.php')
                        .then(response => response.json())
                        .then(data => {
                            roles = data.data;
                            renderTable(roles);
                            addRoleModal.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Chức vụ thêm thành công';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu chức vụ:', error));
                } else {
                    const errorContainer = addRoleModal.querySelector('.modal-error');
                    errorContainer.textContent = result.message || 'Có lỗi khi thêm chức vụ';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    addRoleModal.scrollTop = 0;
                }
            })
            .catch(error => {
                console.error('Thêm chức vụ thất bại:', error);
                const addRoleModal = document.getElementById("add-modal");
                addRoleModal.scrollTop = 0;
            });
        }

        // Hàm xóa chức vụ (cập nhật status_id thành 6)
        function deleteRole(roleId) {
            const formData = new FormData();
            formData.append('id', roleId);
            formData.append('status_id', "6");

            fetch('../../BackEnd/Model/quanlichucvu/xulichucvu.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    fetch('quanlichucvu/fetch_chucvu.php')
                        .then(response => response.json())
                        .then(data => {
                            roles = data.data;
                            renderTable(roles);
                            const deleteModalEl = document.getElementById('delete-modal');
                            deleteModalEl.close();
                            const successMessage = document.getElementById('success-message');
                            successMessage.querySelector('.success-text p').textContent = result.message || 'Chức vụ đã được đánh dấu xóa';
                            successMessage.style.display = 'block';
                            setTimeout(() => {
                                successMessage.style.display = 'none';
                            }, 3000);
                        })
                        .catch(error => console.error('Có lỗi khi lấy dữ liệu chức vụ:', error));
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
            const roleId = parseInt(deleteModalEl.getAttribute('data-role-id'));
            deleteRole(roleId);
        });

        // Hàm xử lý modal Add
        function addViewRoleModalEventListener() {
            const addRoleModal = document.getElementById("add-modal");
            const formEl = document.getElementById("modal-add-form");
            const addCloseButton = addRoleModal.querySelector("#add-close-button");
            const addRoleToolbar = document.querySelector("#add-product-toolbar");

            addRoleToolbar.addEventListener("click", () => {
                addRoleModal.showModal();
            });

            addCloseButton.addEventListener("click", () => {
                addRoleModal.close();
            });

            formEl.addEventListener("submit", (e) => {
                e.preventDefault();
                const isError = validateAddModalFormInputs(formEl);
                if (!isError) {
                    addRole(formEl);
                } else {
                    addRoleModal.scrollTop = 0;
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

                if (input.id === 'modal-edit-name') {
                    if (!/^[\p{L}\s-]+$/u.test(value)) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên chức vụ chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                    } else if (value.length > 50) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên chức vụ không được vượt quá 50 ký tự';
                    }
                }

                if (input.id === 'modal-edit-description') {
                    if (value.length > 200) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 200 ký tự';
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
                    if (!/^[\p{L}\s-]+$/u.test(value)) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên chức vụ chỉ chứa chữ cái, khoảng trắng, và dấu gạch ngang';
                    } else if (value.length > 50) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Tên chức vụ không được vượt quá 50 ký tự';
                    }
                }

                if (input.id === 'modal-add-description') {
                    if (value.length > 200) {
                        isError = true;
                        input.style.border = '1px solid var(--clr-error)';
                        if (errorEl) errorEl.textContent = 'Mô tả không được vượt quá 200 ký tự';
                    }
                }
            });

            return isError;
        }

        // Hàm thêm dữ liệu vào modal
        function addModalData(modalEl, role, type) {
            if (type === "innerHTML") {
                modalEl.querySelector("#modal-view-role-id").textContent = role.id || 'N/A';
                modalEl.querySelector("#modal-view-name").textContent = role.role_name || 'N/A';
                modalEl.querySelector("#modal-view-description").textContent = role.role_description || 'N/A';
                modalEl.querySelector("#modal-view-status").textContent = getStatusText(role.status_id);
            } else if (type === "value") {
                modalEl.querySelector("#modal-edit-role-id").value = role.id;
                modalEl.querySelector("#modal-edit-name").value = role.role_name || '';
                modalEl.querySelector("#modal-edit-description").value = role.role_description || '';
                modalEl.querySelector("#modal-edit-status").value = role.status_id;
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
        addViewRoleModalEventListener();
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
        const permissionModal = document.getElementById('permission-modal');
        if (permissionModal) {
            addModalCancelButtonEventListener(permissionModal);
            const permissionForm = document.getElementById('modal-permission-form');
            permissionForm.addEventListener('submit', (e) => {
                e.preventDefault();
                updatePermission(permissionForm);
            });
        }
    });
    </script>

    <?php
        include 'quanlichucvu/themchucvu.php';
        include 'quanlichucvu/suachucvu.php';
        include 'quanlichucvu/xemchucvu.php';
        include 'quanlichucvu/xoachucvu.php';
        include 'quanlichucvu/menuphanquyen.php';
    ?>
</div>
</body>
</html>