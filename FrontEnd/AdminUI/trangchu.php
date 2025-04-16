<div class="welcome-container">
    <h1 class="welcome-title">CHÀO MỪNG ĐẾN VỚI TRANG ADMIN</h1>
    <p class="welcome-subtitle">CỬA HÀNG ĐIỆN THOẠI NHỎ</p>
    <div class="welcome-animation"></div>
</div>

<style>
.welcome-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 80vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    text-align: center;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    margin: 20px;
}

.welcome-title {
    font-size: 3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    animation: fadeIn 1.5s ease-in-out;
}

.welcome-subtitle {
    font-size: 1.5rem;
    font-weight: 400;
    color: #7f8c8d;
    margin-bottom: 20px;
    animation: fadeIn 2s ease-in-out;
}

.welcome-animation {
    width: 100px;
    height: 5px;
    background: #3498db;
    border-radius: 5px;
    animation: pulse 2s infinite ease-in-out;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scaleX(1);
    }
    50% {
        transform: scaleX(1.5);
    }
    100% {
        transform: scaleX(1);
    }
}

@media (max-width: 768px) {
    .welcome-title {
        font-size: 2rem;
    }
    .welcome-subtitle {
        font-size: 1.2rem;
    }
}
</style>