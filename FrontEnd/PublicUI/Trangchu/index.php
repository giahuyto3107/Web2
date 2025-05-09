<!-- D:\xampp\htdocs\Web2\FrontEnd/PublicUI/Trangchu/index.php -->
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

    <style>
        /* CSS cho khung chat */
        #chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        #chat-icon:hover {
            background-color: #0056b3;
        }
        #chat-icon i {
            color: white;
            font-size: 24px;
        }
        #chat-box {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 450px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 1000;
            font-family: 'Poppins', sans-serif;
        }
        #chat-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-weight: 500;
            text-align: center;
        }
        #chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #f9f9f9;
        }
        #chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
            background-color: #fff;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        #chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        #chat-input button {
            padding: 10px 15px;
            margin-left: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #chat-input button:hover {
            background-color: #0056b3;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .user-message {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }
        .ai-message {
            background-color: #e9ecef;
            color: #333;
            align-self: flex-start;
        }
        .message-avatar {
            width: 40px;
            height: 30px;
            border-radius: 20%;
            margin-right: 10px;
            display: inline-block;
        }
        .ai-avatar {
            background-color: #007bff;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
        }
        .user-avatar {
            background-color: #28a745;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="content-wrapper" id="content-wrapper" style="margin-top: 100px;">
    <!-- Nội dung sẽ được load qua AJAX -->
</div>
<?php include 'footer.php'; ?>

<!-- Biểu tượng chat -->
<div id="chat-icon" onclick="toggleChat()">
    <i class="fas fa-comment"></i>
</div>

<!-- Khung chat -->
<div id="chat-box">
    <div id="chat-header">Trò chuyện với AI</div>
    <div id="chat-messages"></div>
    <div id="chat-input">
        <input type="text" id="chat-input-text" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>

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
        link.removeEventListener('click', handleLinkClick);
        link.addEventListener('click', handleLinkClick);
    });
}

function handleLinkClick(e) {
    e.preventDefault();
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

// Hàm điều khiển khung chat
function toggleChat() {
    const chatBox = document.getElementById('chat-box');
    if (chatBox.style.display === 'none' || chatBox.style.display === '') {
        chatBox.style.display = 'flex';
        // Thêm thông báo chào mừng nếu chưa có tin nhắn
        if (!document.querySelector('#chat-messages .message')) {
            addMessage('Xin chào! Tôi là AI trợ lý. Hãy hỏi tôi về sách!', 'ai-message');
        }
    } else {
        chatBox.style.display = 'none';
    }
}

function sendMessage() {
    const input = document.getElementById('chat-input-text');
    const message = input.value.trim();
    if (!message) return;

    // Thêm tin nhắn của người dùng
    addMessage(message, 'user-message');

    // Gửi yêu cầu đến a2a_server.py qua JSON-RPC
    fetch('http://localhost:5001/jsonrpc', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "jsonrpc": "2.0",
            "method": "recommend_book",
            "params": {
                "input": message,
                "user_id": "1" // Thay bằng user_id thực tế nếu có
            },
            "id": 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result) {
            addMessage(data.result, 'ai-message');
        } else if (data.error) {
            addMessage('Lỗi: ' + data.error.message, 'ai-message');
        }
    })
    .catch(error => {
        console.error('Lỗi kết nối server:', error);
        addMessage('Không thể kết nối với AI. Vui lòng thử lại!', 'ai-message');
    });

    input.value = '';
}

function addMessage(text, className) {
    const messages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${className}`;
    
    // Thêm avatar
    const avatar = document.createElement('div');
    avatar.className = 'message-avatar';
    if (className === 'ai-message') {
        avatar.classList.add('ai-avatar');
        avatar.textContent = 'AI';
    } else {
        avatar.classList.add('user-avatar');
        avatar.textContent = 'Bạn';
    }
    
    // Thêm nội dung tin nhắn
    const messageContent = document.createElement('div');
    messageContent.textContent = text;
    
    // Cấu trúc tin nhắn
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(messageContent);
    
    messages.appendChild(messageDiv);
    messages.scrollTop = messages.scrollHeight;
}
</script>
</body>
</html>