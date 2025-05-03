<footer>
    <div class="row1">
        <div class="col smaller-col">
            <div class="logo">
                <a href=""><span>Góc Sách Nhỏ</span></a>
            </div>
            <p>Ở đâu bán rẻ chúng tôi bán rẻ hơn</p>
        </div>
        <div class="col">
            <h3>Office <div class="underline"><span></span></div></h3>
            <p>958 LacLongQuan Street</p>
            <p>TanBinh District</p>
            <p>HoChiMinh City</p>
            <p class="email-id">imnotgay@yahoo.com</p>
            <h4>+84 22 2222 2222</h4>
        </div>
        <div class="col smallest-col">
            <h3>Links <div class="underline"><span></span></div></h3>
            <ul>
                <li><a href=""><strong>Home</strong></a></li>
                <li><a href=""><strong>Service</strong></a></li>
                <li><a href=""><strong>About us</strong></a></li>
                <li><a href=""><strong>Contact</strong></a></li>
                <li><a href=""><strong>Policy</strong></a></li>
            </ul>
        </div>
        <div class="col biggest-col">
            <h3>Chăm sóc KH <div class="underline"><span></span></div></h3>
            <div class="box-two__call mb-3 box-content">
                <ul class="list-link">
                    <li class="link"><div><strong>Gọi mua hàng</strong> <a href="tel:18002097">1800.2097</a> (7h30 - 22h00)</div></li>
                    <li class="link"><div><strong>Gọi khiếu nại</strong> <a href="tel:18002063">1800.2063</a> (8h00 - 21h30)</div></li>
                    <li class="link"><div><strong>Gọi bảo hành</strong> <a href="tel:18002064">1800.2064</a> (8h00 - 21h00)</div></li>
                </ul>
            </div>
            <!-- <div class="payment-methods">
                <div class="pay-gate-way__title">
                    <p class="mb-3 title"><strong>Phương thức thanh toán</strong></p>
                </div>
                <div class="pay-gate-way__content">
                    <ul class="list-link">
                        <li class="link border icon-cps rounded">
                            <a href=""><img src="https://i.pinimg.com/736x/3e/35/15/3e3515428abe4843bb69cf936e404090.jpg" alt="COD" loading="lazy"></a>
                        </li>
                        <li class="link border icon-cps rounded">
                            <a href=""><img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/6/0oxhzjmxbksr1686814746087.png" alt="Vnpay" loading="lazy"></a>
                        </li>
                        <li class="link border icon-cps rounded">
                            <a href=""><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQCp0JctwLH5Hgagb0TY-xvAuWK2NCGU4fZgQ&s" alt="MoMo" loading="lazy"></a>
                        </li>
                    </ul>
                </div>
            </div> -->
        </div>
        <div class="col">
            <h3>NewsLetter <div class="underline"><span></span></div></h3>
            <form>
                <i class="fa-solid fa-envelope"></i>
                <input type="email" placeholder="Nhập email của bạn" required>
                <button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
            <p>Đăng ký nhận tin khuyến mãi để nhận voucher 10%</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/truong.mau.ien"><i class="fa-brands fa-facebook"></i></a>
                <a href="https://x.com/notifications"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="https://www.instagram.com/cristiano/?hl=en"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <hr>
    <p class="copyright"><span>Góc Sách Nhỏ</span> Copyright © 2025 - Privacy Policy</p>
</footer>

<style>
    /* Footer Styles */
footer {
    margin-top: 20px;
    background: #111111;
    color: #ffffff;
    padding: 40px 0 20px;
    width: 100%;
}

.row1 {
    width: 85%;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap; /* Cho phép wrap khi màn hình nhỏ */
}

.col {
    flex-basis: 20%;
    padding: 10px;
}

.smaller-col {
    flex-basis: 15%;
}

.smallest-col {
    flex-basis: 10%;
}

.biggest-col {
    flex-basis: 25%;
}

.logo a {
    text-decoration: none;
}

ul {
    padding-left: 0;
}

.logo span {
    font-size: 1.5rem;
    font-weight: 400;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.col p {
    font-size: 0.9rem;
    font-weight: 300;
    color: #ffffff;
    margin-top: 15px;
}

.col h3 {
    font-size: 1rem;
    font-weight: 400;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    position: relative;
    margin-bottom: 25px;
}

.underline {
    width: 40px;
    height: 1px;
    background: #cccccc;
    position: absolute;
    bottom: -8px;
    left: 0;
}

.underline span {
    display: none;
}

.email-id {
    font-size: 0.9rem;
    color: #ffffff;
    border-bottom: 1px solid #cccccc;
    padding-bottom: 2px;
}

h4 {
    font-size: 0.9rem;
    font-weight: 300;
    color: #ffffff;
    margin-top: 5px;
}

ul {
    list-style: none;
}

ul li {
    margin-bottom: 10px;
    border: none !important;
}

ul li a {
    font-size: 0.9rem;
    font-weight: 300;
    color: #ffffff;
    text-decoration: none;
    transition: color 0.3s ease;
    border: none !important;
}

ul li a:hover {
    color: #cccccc;
}

ul li a strong {
    font-weight: 400;
}

.box-two__call .list-link li {
    margin-bottom: 15px;
}

.payment-methods {
    margin-top: 20px;
}

.payment-methods .title {
    font-size: 0.9rem;
    font-weight: 400;
    color: #ffffff;
    margin-bottom: 15px;
}

.payment-methods .list-link {
    display: flex;
    gap: 10px;
}

.payment-methods .link {
    flex: 1;
    text-align: center;
}

.border {
    border-width: 0px;
}

.payment-methods img {
    width: 50px;
    height: auto;
    border-radius: 4px;
    transition: border-color 0.3s ease;
}

footer form {
    display: flex;
    align-items: center;
    border-bottom: 1px solid #cccccc;
    padding-bottom: 5px;
    margin-bottom: 15px;
}

footer form .fa-envelope {
    font-size: 16px;
    color: #ffffff;
    margin-right: 10px;
}

footer form input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    font-size: 0.9rem;
    font-weight: 300;
    color: #ffffff;
}

footer form input::placeholder {
    color: #cccccc;
}

footer form button {
    background: transparent;
    border: none;
    cursor: pointer;
}

footer form .fa-arrow-right {
    font-size: 16px;
    color: #ffffff;
    transition: color 0.3s ease;
}

footer form button:hover .fa-arrow-right {
    color: #cccccc;
}

.social-icons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.social-icons a {
    text-decoration: none;
}

.social-icons .fa-brands {
    font-size: 20px;
    color: #ffffff;
    transition: color 0.3s ease;
}

.social-icons .fa-brands:hover {
    color: #cccccc;
}

hr {
    width: 90%;
    border: none;
    border-bottom: 1px solid #cccccc;
    margin: 30px auto;
    opacity: 0.5;
}

.copyright {
    text-align: center;
    font-size: 0.9rem;
    font-weight: 300;
    color: #ffffff;
}

.copyright span {
    color: #ffffff;
    font-weight: 400;
}

/* Responsive */
@media (max-width: 1024px) {
    .row1 {
        width: 90%;
    }

    .col, .smaller-col, .smallest-col, .biggest-col {
        flex-basis: 50%; /* 2 cột trên tablet */
        margin-bottom: 20px;
    }

    .logo span {
        font-size: 1.4rem;
    }

    .col h3 {
        font-size: 0.95rem;
    }

    .col p, .email-id, h4, ul li a, .payment-methods .title {
        font-size: 0.85rem;
    }

    .payment-methods img {
        width: 45px;
    }

    .social-icons .fa-brands {
        font-size: 18px;
    }
}

@media (max-width: 768px) {
    footer {
        padding: 30px 0 15px;
    }

    .row1 {
        width: 95%;
        flex-direction: column; /* Xếp chồng thành 1 cột */
    }

    .col, .smaller-col, .smallest-col, .biggest-col {
        flex-basis: 100%;
        padding: 8px;
        margin-bottom: 15px;
    }

    .logo span {
        font-size: 1.3rem;
    }

    .col h3 {
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .underline {
        width: 35px;
    }

    .col p, .email-id, h4, ul li a, .payment-methods .title {
        font-size: 0.8rem;
    }

    .payment-methods img {
        width: 40px;
    }

    .payment-methods .list-link {
        gap: 8px;
    }

    footer form .fa-envelope, footer form .fa-arrow-right {
        font-size: 14px;
    }

    footer form input {
        font-size: 0.8rem;
    }

    .social-icons {
        gap: 12px;
        margin-top: 10px;
    }

    .social-icons .fa-brands {
        font-size: 16px;
    }

    hr {
        margin: 20px auto;
    }

    .copyright {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    footer {
        padding: 20px 0 10px;
    }

    .row1 {
        width: 100%;
        padding: 0 10px;
    }

    .logo span {
        font-size: 1.2rem;
    }

    .col h3 {
        font-size: 0.85rem;
    }

    .col p, .email-id, h4, ul li a, .payment-methods .title {
        font-size: 0.75rem;
    }

    .payment-methods img {
        width: 35px;
    }

    .payment-methods .list-link {
        gap: 6px;
    }

    footer form {
        margin-bottom: 10px;
    }

    footer form .fa-envelope, footer form .fa-arrow-right {
        font-size: 12px;
    }

    footer form input {
        font-size: 0.75rem;
    }

    .social-icons {
        gap: 10px;
    }

    .social-icons .fa-brands {
        font-size: 14px;
    }

    hr {
        margin: 15px auto;
    }

    .copyright {
        font-size: 0.75rem;
    }
}
</style>