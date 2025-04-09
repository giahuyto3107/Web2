<!-- D:\xampp\htdocs\Web2\FrontEnd\PublicUI\Trangchu\index.php -->
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--Start of Fchat.vn--><script type="text/javascript" src="https://cdn.fchat.vn/assets/embed/webchat.js?id=67304dce83030804da413384" async="async"></script><!--End of Fchat.vn-->
</head>
<body>
<div class="content-wrapper" id="content-wrapper" style="margin-top: 100px;">
    <!-- Nội dung sẽ được load qua AJAX -->
</div>
<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = urlParams.get('page') || 'home';
    const orderId = urlParams.get('order_id');
    const productId = urlParams.get('id');
    console.log('Initial page:', initialPage, 'Order ID:', orderId, 'Product ID:', productId);
    if (orderId) {
        loadPage(`orders&order_id=${orderId}`);
    } else if (productId && initialPage === 'product_details') {
        loadPage(`product_details&id=${productId}`);
    } else {
        loadPage(initialPage);
    }

    attachLinkEvents();
    document.getElementById('content-wrapper').addEventListener('click', function(e) {
        const link = e.target.closest('a[data-page]');
        if (link) {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            console.log('Clicked page:', page);
            loadPage(page);
        }
    });
});

function attachLinkEvents() {
    document.querySelectorAll('a[data-page]').forEach(link => {
        link.removeEventListener('click', handleLinkClick); // Xóa sự kiện cũ để tránh trùng lặp
        link.addEventListener('click', handleLinkClick);
    });
}

function handleLinkClick(e) {
    e.preventDefault(); // Ngăn reload trang
    const page = this.getAttribute('data-page');
    console.log('Clicked page:', page);
    loadPage(page);
}

function loadPage(page) {
    console.log('Loading page:', page);
    fetch(`http://localhost/web2/FrontEnd/PublicUI/Trangchu/load_page.php?page=${encodeURIComponent(page)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
    })
    .then(content => {
        const contentWrapper = document.getElementById('content-wrapper');
        contentWrapper.innerHTML = content;
        const url = new URL(window.location.origin + '/Web2/FrontEnd/PublicUI/Trangchu/index.php');
        url.search = `?page=${page}`;
        window.history.pushState({}, '', url);
        executeScripts(content);
        attachLinkEvents(); 
        window.scrollTo(0, 0);
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        document.getElementById('content-wrapper').innerHTML = `<p>Có lỗi khi tải trang: ${error.message}</p>`;
    });
}

function executeScripts(content) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;
    const scripts = tempDiv.querySelectorAll('script');
    console.log('Scripts found:', scripts.length);
    scripts.forEach(script => {
        const newScript = document.createElement('script');
        if (script.src) {
            newScript.src = script.src;
            newScript.async = false;
        } else {
            newScript.textContent = script.textContent;
        }
        document.head.appendChild(newScript).parentNode.removeChild(newScript);
    });
}

window.onpopstate = function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'home';
    const orderId = urlParams.get('order_id');
    const productId = urlParams.get('id');
    if (orderId) {
        loadPage(`orders&order_id=${orderId}`);
    } else if (productId && page === 'product_details') {
        loadPage(`product_details&id=${productId}`);
    } else {
        loadPage(page);
    }
};
</script>
</body>
</html>