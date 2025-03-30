
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
        <h1 class="heading"> Quản lý <span>BÌNH LUẬN</span></h1>
        <div class="toolbar">
            <div class="filters">
                <div class="filter-options-wrapper">
                    <label for="filter-options" class="filter-label">Bộ lọc </label>
                    <select id="filter-options">
                        <option value="user_name">Tên người dùng</option>
                        <option value="admin_name">Tên Admin</option>
                        <option value="product_name">Tên sản phẩm</option>
                        <option value="rating">Đánh giá</option>
                        <option value="review_text">Nội dung đánh giá</option>
                        <option value="status_id">Trạng thái</option>
                    </select>
                </div>
                <div class="search">
                    <input type="text" id="search-text" name="search-text" placeholder="Tìm kiếm..." />
                </div>
            </div>
        </div>

        <div id="selected-reviews"></div>

        <div class="table-container">
            <div class="no-reviews">
                <p>Looks like you do not have any reviews.</p>
            </div>

            <table class="table" id="data-table">
                <thead>
                    <tr>
                        <th data-id="review_id">ID</th>
                        <th data-id="user_name">Tên người dùng</th>
                        <th data-id="admin_name">Tên Admin</th>
                        <th data-id="product_name">Tên sản phẩm</th>
                        <th data-id="rating">Đánh giá</th>
                        <th data-id="review_text">Nội dung</th>
                        <th data-id="review_date">Ngày đánh giá</th>
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
            let reviews = []; // Dữ liệu gốc, không thay đổi

            // Hàm chuyển status_id thành văn bản
            function getStatusText(statusId) {
                switch (statusId) {
                    case "1": return 'Active';
                    case "2": return 'Inactive';
                    case "6": return 'Deleted';
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

                    let filteredData = reviews;

                    if (searchValue !== "") {
                        filteredData = reviews.filter((review) => {
                            if (typeof review[filterBy] === "string") {
                                return review[filterBy].toLowerCase().includes(searchValue.toLowerCase());
                            } else if (typeof review[filterBy] === "number") {
                                return review[filterBy].toString().includes(searchValue);
                            }
                            return false;
                        });
                    }

                    renderTable(filteredData);
                });

                filterOptionsEl.addEventListener("change", () => {
                    searchEl.value = "";
                    renderTable(reviews);
                });
            }

            // Hàm render bảng
            function renderTable(displayedReviews) {
                const tableBody = document.getElementById('table-body');
                const noReviewsEl = document.querySelector('.no-reviews');

                if (!tableBody || !noReviewsEl) {
                    console.error('Required elements not found: #table-body or .no-reviews');
                    return;
                }

                tableBody.innerHTML = '';
                const activeReviews = displayedReviews.filter(review => review.status_id !== 6);

                if (activeReviews.length > 0) {
                    noReviewsEl.style.display = 'none';
                    activeReviews.forEach((review, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${review.user_name || 'N/A'}</td>
                            <td>${review.admin_name || 'N/A'}</td>
                            <td>${review.product_name || 'N/A'}</td>
                            <td>${review.rating || 'N/A'}</td>
                            <td>${review.review_text || 'N/A'}</td>
                            <td>${review.review_date || 'N/A'}</td>
                            <td>${getStatusText(review.status_id)}</td>
                            <td class="actions">
                                <div class="dropdown">
                                    <button class="dropdownButton"><i class="fa fa-ellipsis-v dropIcon"></i></button>
                                    <div class="dropdown-content">
                                        <a href="#" class="viewReview" data-review-id="${review.review_id}">Xem Bình Luận <i class="fa fa-eye"></i></a>
                                        <a href="#" class="editReview" data-review-id="${review.review_id}">Sửa Bình Luận <i class="fa fa-edit"></i></a>
                                        <a href="#" class="deleteReview" data-review-id="${review.review_id}">Xóa Bình Luận <i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    noReviewsEl.style.display = 'flex';
                    tableBody.innerHTML = '<tr><td colspan="9">Không tìm thấy bình luận nào.</td></tr>';
                }
            }

            // Fetch dữ liệu ban đầu từ server
            fetch('quanlibinhluan/fetch_reviews.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        reviews = data.data;
                        console.log('Initial reviews:', reviews);
                        renderTable(reviews);
                        addFilterEventListener();
                    } else {
                        console.error('Error:', data.message);
                        document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách bình luận.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('table-body').innerHTML = '<tr><td colspan="9">Lỗi khi tải danh sách bình luận.</td></tr>';
                });

            // Sử dụng event delegation để xử lý các hành động
            document.getElementById('table-body').addEventListener('click', (e) => {
                const target = e.target.closest('a');
                if (!target) return;

                e.preventDefault();
                const reviewId = target.getAttribute('data-review-id');
                const review = reviews.find(rev => rev.review_id === reviewId);

                if (!review) {
                    console.error('Review not found:', reviewId);
                    return;
                }

                if (target.classList.contains('viewReview')) {
                    const viewModalEl = document.getElementById("view-modal");
                    addModalData(viewModalEl, review, "innerHTML");
                    viewModalEl.showModal();
                } else if (target.classList.contains('editReview')) {
                    const editModalEl = document.getElementById("edit-modal");
                    openEditModal(review);
                } else if (target.classList.contains('deleteReview')) {
                    const deleteModalEl = document.getElementById("delete-modal");
                    deleteModalEl.setAttribute("data-review-id", reviewId);
                    deleteModalEl.showModal();
                }
            });

            // Hàm mở modal chỉnh sửa
            function openEditModal(review) {
                const editModal = document.getElementById('edit-modal');
                const form = document.getElementById('modal-edit-form');

                document.getElementById('modal-edit-review-id').value = review.review_id;
                document.getElementById('modal-edit-review-text').value = review.review_text || '';
                document.getElementById('modal-edit-feedback').value = review.feedback || '';
                document.getElementById('modal-edit-status').value = review.status_id;

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
                    errorContainer.textContent = 'Vui lòng sửa các lỗi trên form trước khi submit.';
                    errorContainer.style.display = 'block';
                    errorContainer.style.color = 'var(--clr-error)';
                    editModal.scrollTop = 0;
                    return;
                }

                updateReview(form);
            }

            // Hàm cập nhật đánh giá
            function updateReview(form) {
                const formData = new FormData(form);
                fetch('../../BackEnd/Model/quanlibinhluan/xulibinhluan.php', {
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
                        fetch('quanlibinhluan/fetch_reviews.php')
                            .then(response => response.json())
                            .then(data => {
                                reviews = data.data;
                                renderTable(reviews);
                                editModal.close();
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Bình luận đã được cập nhật';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu bình luận:', error));
                    } else {
                        const errorContainer = editModal.querySelector('.modal-error');
                        errorContainer.textContent = result.message || 'Có lỗi khi cập nhật bình luận';
                        errorContainer.style.display = 'block';
                        errorContainer.style.color = 'var(--clr-error)';
                        editModal.scrollTop = 0;
                    }
                })
                .catch(error => {
                    console.error('Cập nhật bình luận thất bại:', error);
                    const editModal = document.getElementById('edit-modal');
                    editModal.scrollTop = 0;
                });
            }

            // Hàm xóa đánh giá (cập nhật status_id thành 6)
            function deleteReview(reviewId) {
                const formData = new FormData();
                formData.append('review_id', reviewId);
                formData.append('status_id', 6);

                fetch('../../BackEnd/Model/quanlibinhluan/xulibinhluan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        fetch('quanlibinhluan/fetch_reviews.php')
                            .then(response => response.json())
                            .then(data => {
                                reviews = data.data;
                                renderTable(reviews);
                                const deleteModalEl = document.getElementById('delete-modal');
                                deleteModalEl.close();
                                const successMessage = document.getElementById('success-message');
                                successMessage.querySelector('.success-text p').textContent = result.message || 'Bình luận đã được đánh dấu xóa';
                                successMessage.style.display = 'block';
                                setTimeout(() => {
                                    successMessage.style.display = 'none';
                                }, 3000);
                            })
                            .catch(error => console.error('Có lỗi khi lấy dữ liệu bình luận:', error));
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
                const reviewId = parseInt(deleteModalEl.getAttribute('data-review-id'));
                deleteReview(reviewId);
            });

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

                    if (input.id === 'modal-edit-feedback') {
                        if (value.length > 200) {
                            isError = true;
                            input.style.border = '1px solid var(--clr-error)';
                            if (errorEl) errorEl.textContent = 'Phản hồi không được vượt quá 200 ký tự';
                        }
                    }
                });

                return isError;
            }

            // Hàm thêm dữ liệu vào modal
            function addModalData(modalEl, review, type) {
                if (type === "innerHTML") {
                    modalEl.querySelector("#modal-view-review-id").textContent = review.review_id || 'N/A';
                    modalEl.querySelector("#modal-view-user-name").textContent = review.user_name || 'N/A';
                    modalEl.querySelector("#modal-view-admin-name").textContent = review.admin_name || 'N/A';
                    modalEl.querySelector("#modal-view-product-name").textContent = review.product_name || 'N/A';
                    modalEl.querySelector("#modal-view-rating").textContent = review.rating || 'N/A';
                    modalEl.querySelector("#modal-view-review-text").textContent = review.review_text || 'N/A';
                    modalEl.querySelector("#modal-view-feedback").textContent = review.feedback || 'N/A';
                    modalEl.querySelector("#modal-view-review-date").textContent = review.review_date || 'N/A';
                    modalEl.querySelector("#modal-view-status").textContent = getStatusText(review.status_id);
                } else if (type === "value") {
                    modalEl.querySelector("#modal-edit-review-id").value = review.review_id;
                    modalEl.querySelector("#modal-edit-review-text").value = review.review_text || '';
                    modalEl.querySelector("#modal-edit-feedback").value = review.feedback || '';
                    modalEl.querySelector("#modal-edit-status").value = review.status_id;
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
            addModalCloseButtonEventListeners();
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

    <!-- Modal -->
    <dialog data-modal id="view-modal">
        <div class="modal-header">
            <h2>Chi tiết Bình Luận</h2>
            <button class="modal-close" data-id="view-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="view-container">
            <div class="view-content">
                <span>ID</span>
                <p id="modal-view-review-id">N/A</p>
            </div>
            <div class="view-content">
                <span>Tên người dùng</span>
                <p id="modal-view-user-name">N/A</p>
            </div>
            <div class="view-content">
                <span>Tên Admin</span>
                <p id="modal-view-admin-name">N/A</p>
            </div>
            <div class="view-content">
                <span>Tên sản phẩm</span>
                <p id="modal-view-product-name">N/A</p>
            </div>
            <div class="view-content">
                <span>Đánh giá</span>
                <p id="modal-view-rating">N/A</p>
            </div>
            <div class="view-content">
                <span>Nội dung</span>
                <p id="modal-view-review-text">N/A</p>
            </div>
            <div class="view-content">
                <span>Phản hồi</span>
                <p id="modal-view-feedback">N/A</p>
            </div>
            <div class="view-content">
                <span>Ngày đánh giá</span>
                <p id="modal-view-review-date">N/A</p>
            </div>
            <div class="view-content">
                <span>Trạng thái</span>
                <p id="modal-view-status">N/A</p>
            </div>
            <div class="modal-buttons">
                <button class="close" id="view-close-button">Đóng</button>
            </div>
        </div>
    </dialog>

    <dialog data-modal id="edit-modal">
        <div class="modal-header">
            <h2>Chỉnh sửa Bình Luận</h2>
            <button class="modal-close" data-id="edit-modal">
                <i class="fa fa-times" style="font-size: 1.5rem; height: 1.5rem"></i>
            </button>
        </div>
        <div class="modal-content">
            <form id="modal-edit-form" class="modal-form">
                <input type="hidden" id="modal-edit-review-id" name="review_id" />
                
                <div class="modal-input">
                    <span>Nội dung</span>
                    <textarea id="modal-edit-review-text" name="review_text" readonly></textarea>
                    <p class="modal-error" id="modal-edit-review-text-error"></p>
                </div>
                <div class="modal-input">
                    <span>Phản hồi</span>
                    <textarea id="modal-edit-feedback" name="feedback"></textarea>
                    <p class="modal-error" id="modal-edit-feedback-error"></p>
                </div>
                <div class="modal-input">
                    <span>Trạng thái</span>
                    <select id="modal-edit-status" name="status_id" required>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                    <p class="modal-error" id="modal-edit-status-error"></p>
                </div>
                <div class="modal-buttons">
                    <button class="close" id="edit-close-button">Hủy</button>
                    <button type="submit" class="save">Lưu</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog data-modal id="delete-modal">
        <div class="delete-modal-wrapper">
            <h2>Cảnh báo!</h2>
            <div class="delete-modal-text">
                <p>Bạn có muốn xóa bình luận này?</p>
                <div class="modal-buttons">
                    <button class="cancel" id="delete-close-button">Hủy bỏ</button>
                    <button class="delete" id="delete-delete-button">Xóa bình luận</button>
                </div>
            </div>
        </div>
    </dialog>
</body>
</html>