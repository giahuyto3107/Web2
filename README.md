# Hướng Dẫn Cài Đặt và Chạy Đồ Án Web2

Đồ án này là một hệ thống web với **giao diện người dùng (Frontend)**, **backend cơ bản (PHP)**, và một **server AI (A2A)** để gợi ý sách. Hệ thống sử dụng tiếng Việt và tiền tệ Việt Nam (VND). Hướng dẫn này sẽ giúp bạn cài đặt và chạy toàn bộ dự án trên máy Windows, với tùy chọn sử dụng máy ảo WSL cho server A2A.

## Yêu Cầu Hệ Thống

Trước khi bắt đầu, hãy đảm bảo máy tính của bạn đã cài đặt các phần mềm sau:

- [XAMPP](https://www.apachefriends.org/download.html) (chỉ cần chạy Apache)
- [Python 3.7+](https://www.python.org/downloads/)
- [Git](https://git-scm.com/downloads)
- [MySQL Workbench](https://dev.mysql.com/downloads/workbench/)
- Trình duyệt web (Google Chrome, Firefox, v.v.)
- Trình chỉnh sửa mã nguồn (gợi ý: [Visual Studio Code](https://code.visualstudio.com/))

## Hướng Dẫn Cài Đặt

### 1. Thiết Lập Frontend và Backend

#### 1.1. Cài đặt XAMPP
- Tải và cài đặt XAMPP từ [link trên](https://www.apachefriends.org/download.html).
- Khởi động XAMPP Control Panel, chỉ bật **Apache** (không cần bật MySQL vì chúng ta sẽ dùng MySQL Workbench).

#### 1.2. Sao chép mã nguồn Frontend
- Sao chép thư mục `Web2` (bao gồm `FrontEnd/PublicUI/Trangchu`) vào thư mục `C:\xampp\htdocs\`.
- Đường dẫn cuối cùng sẽ là: `C:\xampp\htdocs\Web2\FrontEnd\PublicUI\Trangchu`.
- Đảm bảo các file cần thiết (`index.php`, `header.php`, `footer.php`, `load_page.php`, `config.php`, `database.php`) đã có trong thư mục `Trangchu`.

#### 1.3. Thiết lập MySQL với MySQL Workbench
- Cài đặt và mở MySQL Workbench.
- Tạo một kết nối mới:
  - Nhấn `+` bên cạnh "MySQL Connections".
  - Đặt tên kết nối (ví dụ: Local), hostname: `127.0.0.1`, port: `3306`, username: `root`, password: `1234` (hoặc mật khẩu bạn đã đặt).
  - Nhấn OK để lưu.
- Khởi động server MySQL:
  - Trong MySQL Workbench, vào `Server > Startup/Shutdown` và nhấn `Start Server` nếu server chưa chạy.
- Tạo database và nhập dữ liệu:
  - Mở kết nối vừa tạo
  - Nhập dữ liệu từ file `sql_web2.sql`:
    - Vào `Server > Data Import`.
    - Chọn `Import from Self-Contained File`, duyệt đến file `sql_web2.sql` và nhấn `Start Import`.

#### 1.4. Cấu hình kết nối database
- Mở file `config.php` và `database.php` trong `C:\xampp\MercuryMail\htdocs\Web2\BackEnd\Config\config.php`.
- Chỉnh sửa thông tin kết nối database (host, user, password, database name) cho phù hợp với cấu hình MySQL của bạn. Ví dụ:
  - Trong `database.php`:
    ```php
    <?php
    $host = '127.0.0.1';
    $db_name = 'web2_sql';
    $username = 'root';
    $password = '1234';  // Thay bằng mật khẩu MySQL của bạn
    ?>
    ```
  - Trong `config.php` (nếu có), cập nhật tương tự.

#### 1.5. Kiểm tra Frontend
- Mở trình duyệt và truy cập dành cho tài khoản khách:  
  `http://localhost/web2/FrontEnd/PublicUI/Trangchu/`
- Mở trình duyệt và truy cập dành cho tài khoản quản trị viên:  
  `http://localhost/web2/FrontEnd/AdminUI/`
- Nếu trang web hiển thị giao diện chính, bạn đã thiết lập thành công phần Frontend.

### 2. Thiết Lập Server A2A (AI)

Server A2A có thể được thiết lập trên Windows hoặc máy ảo WSL. Dưới đây là hướng dẫn cho cả hai cách.

#### 2.1. Cài đặt Python
- Tải và cài Python 3.7+ từ [link trên](https://www.python.org/downloads/). Đảm bảo chọn tùy chọn "Add Python to PATH" trong quá trình cài đặt.
- Kiểm tra phiên bản Python:  
  ```bash
  python --version
  ```  
  (ví dụ: Python 3.11.5).

#### 2.2. Thiết lập trên Windows (hệ điều hành hiện tại)

##### 2.2.1. Cài đặt môi trường ảo (khuyến nghị)
- Mở Command Prompt (cmd) hoặc PowerShell trong VSCode (nhấn `Ctrl + ``).
- Điều hướng đến thư mục `a2a_server`:  
  ```bash
  cd C:\xampp\MercuryMail\htdocs\Web2\a2a_server
  ```

##### 2.2.2. Cài đặt thư viện Python

- Cài đặt thư viện:  
  ```bash
  pip install -r requirements.txt
  ```
`PS C:\xampp\MercuryMail\htdocs\Web2\a2a_server> pip install -r requirements.txt`
- nó sẽ như này

##### 2.2.3. Cấu hình biến môi trường
- Tạo file `.env` trong thư mục `a2a_server` với nội dung:
  ```env
  DB_HOST=127.0.0.1
  DB_USER=root
  DB_PASSWORD=1234  # Thay bằng mật khẩu MySQL của bạn
  DB_NAME=web2_sql
  DB_PORT=3306
  GOOGLE_API_KEY=your_google_api_key_here  # Thay bằng API key của bạn (xem phần lấy API key bên dưới)
  ```

##### 2.2.3. Chạy server A2A
- Trong terminal (đã kích hoạt venv), chạy:  
  ```bash
  python a2a_server.py
  ```
- Server sẽ chạy trên `http://localhost:5001`. Kiểm tra log trong `a2a_server.log` để đảm bảo không có lỗi.

#### 2.3. Thiết lập trên WSL (máy ảo)

##### 2.3.1. Cài đặt WSL
-(chưa tìm hiểu kĩ phần này nên có thể sẽ sai sót ở phần kết nối MySQL Workbench với WSL nhé)
- Mở PowerShell với quyền Administrator và cài đặt WSL:  
  ```bash
  wsl --install
  ```
- Khởi động lại máy nếu được yêu cầu.
- Cài đặt Ubuntu (nếu chưa có):
  - Mở Microsoft Store, tìm "Ubuntu" (ví dụ: Ubuntu 20.04), và cài đặt.
  - Mở Ubuntu từ Start Menu, thiết lập username và password cho WSL.

##### 2.3.2. Cài đặt Python trong WSL
- Mở terminal WSL (Ubuntu).
- Cập nhật hệ thống:  
  ```bash
  sudo apt update && sudo apt upgrade -y
  ```
- Cài đặt Python 3 và pip:  
  ```bash
  sudo apt install python3 python3-pip python3-venv -y
  ```
- Kiểm tra phiên bản:  
  ```bash
  python3 --version
  ```

##### 2.3.3. Sao chép mã nguồn A2A vào WSL
- Trong WSL, điều hướng đến thư mục Windows:  
  ```bash
  cd /mnt/c/xampp/MercuryMail/htdocs/Web2/a2a_server
  ```

##### 2.3.4. Cài đặt môi trường ảo và thư viện
- Tạo môi trường ảo:  
  ```bash
  python3 -m venv venv
  ```
- Kích hoạt môi trường ảo:  
  ```bash
  source venv/bin/activate
  ```
- Cài đặt thư viện từ `requirements.txt`:  
  ```bash
  pip install -r requirements.txt
  ```

##### 2.3.5. Cấu hình biến môi trường
- Tạo hoặc chỉnh sửa file `.env` trong `/mnt/c/xampp/MercuryMail/htdocs/Web2/a2a_server`:
  ```env
  DB_HOST=192.168.1.x  # Thay bằng IP của máy Windows (tìm bằng `ipconfig` trong cmd)
  DB_USER=root
  DB_PASSWORD=1234  # Thay bằng mật khẩu MySQL của bạn
  DB_NAME=web2_sql
  DB_PORT=3306
  GOOGLE_API_KEY=your_google_api_key_here  # Thay bằng API key của bạn
  ```
- Để lấy IP của máy Windows, mở cmd và chạy: `ipconfig` (tìm dòng IPv4 Address, ví dụ: 192.168.1.100).

##### 2.3.6. Cấu hình MySQL trên Windows để chấp nhận kết nối từ WSL
- Mở file cấu hình MySQL (thường là `my.ini` trong `C:\ProgramData\MySQL\MySQL Server 8.0\`).
- Sửa dòng `bind-address`: `bind-address = 0.0.0.0`
- Khởi động lại MySQL trong MySQL Workbench (`Server > Startup/Shutdown > Restart`).
- Tạo user cho kết nối từ xa:
  - Trong MySQL Workbench, mở tab `Query` và chạy:
    ```sql
    CREATE USER 'root'@'%' IDENTIFIED BY '1234';
    GRANT ALL PRIVILEGES ON web2_sql.* TO 'root'@'%';
    FLUSH PRIVILEGES;
    ```
- Mở firewall trên Windows:
  - Vào `Control Panel > Windows Defender Firewall > Advanced Settings > Inbound Rules`.
  - Tạo quy tắc mới cho cổng 3306, cho phép kết nối.

##### 2.3.7. Chạy server A2A trong WSL
- Trong terminal WSL (đã kích hoạt venv), chạy:  
  ```bash
  python3 a2a_server.py
  ```
- Server sẽ chạy trên `http://localhost:5001`.

### 3. Lấy API Key của Gemini
- Truy cập [Google Cloud Console](https://console.cloud.google.com/).
- Tạo hoặc chọn một dự án:
  - Nhấn vào menu dự án ở góc trên bên trái, chọn `New Project`, đặt tên (ví dụ: Web2-Gemini), và nhấn `Create`.
- Kích hoạt Gemini API:
  - Vào `APIs & Services > Library`.
  - Tìm "Generative AI" hoặc "Gemini API" và nhấn `Enable`.
- Tạo API Key:
  - Vào `APIs & Services > Credentials`.
  - Nhấn `Create Credentials > API Key`.
  - Sao chép API key (ví dụ: `AIzaSy...`).
- Thêm API key vào file `.env` trong `a2a_server`:
  ```env
  GOOGLE_API_KEY=your_api_key_here  # Thay bằng API key vừa lấy
  ```

### 4. Kiểm Tra Hệ Thống
- Mở trình duyệt và truy cập:  
  `http://localhost/web2/FrontEnd/PublicUI/Trangchu/index.php`
- Nhấp vào biểu tượng chat (góc dưới bên phải) và gửi tin nhắn, ví dụ: "Gợi ý một cuốn sách".
- Hệ thống sẽ trả lời bằng tiếng Việt, ví dụ: "Dựa trên yêu cầu của bạn, tôi gợi ý cuốn 'Nhà Giả Kim' (Giá: 45.000 VND) trong danh mục Tiểu thuyết...".

## Khắc Phục Lỗi Thường Gặp

- **Không thể kết nối với MySQL:**
  - Đảm bảo MySQL đang chạy trong MySQL Workbench.
  - Kiểm tra thông tin trong `.env`, `config.php`, và `database.php` khớp với MySQL.
- **Không thể kết nối với AI:**
  - Mở DevTools (F12) trong trình duyệt, kiểm tra tab Network xem có lỗi CORS không.
  - Đảm bảo `flask-cors` đã được cài đặt và server đang chạy.
- **Lỗi API Gemini:**
  - Kiểm tra `GOOGLE_API_KEY` trong `.env` có hợp lệ không.
  - Đảm bảo bạn đã kích hoạt Gemini API trong Google Cloud Console.
- **Server không chạy trên cổng 5001:**
  - Kiểm tra xem cổng 5001 có bị chiếm không:  
    ```bash
    netstat -an | find "5001"
    ```
  - Nếu bị chiếm, thay đổi cổng trong `a2a_server.py` (ví dụ: `port=5002`) và cập nhật trong `index.php`.

## Lưu Ý

- Chỉnh sửa cấu hình: Nhớ chỉnh sửa `config.php` và `database.php` để phù hợp với thông tin database của bạn (host, user, password, database name).
- File `sql_web2.sql`: Đảm bảo file này chứa dữ liệu mẫu phù hợp với cấu trúc bảng trong database `web2_sql`.

## Tổng quan về AI Gemini tích hợp vào hệ thống và giao thức A2A

- AI có thể trả lời những gì liên quan đến database trừ các thông tin bảo mật người dùng: 
  + Gợi ý sách: Ví dụ "gợi ý một cuốn sách" hoặc "gợi ý sách dưới 50.000 VND".
  + Gợi ý danh mục: Ví dụ "gợi ý danh mục Khoa học".
  + Thông tin loại danh mục: Ví dụ "có bao nhiêu loại danh mục?".
  + Thông tin sách: Ví dụ "có bao nhiêu sách?" hoặc "sách nào phổ biến nhất?".
- Giao thức A2A (A2A protocol) (vì đây là giao thức rất mới nên vẫn sẽ còn tiềm ẩn lỗi)
  + Có thể tham khảo tại website của GOOGLE: https://developers.googleblog.com/en/a2a-a-new-era-of-agent-interoperability/

## Tác Giả

- **Tên:** [Cao Thái Bảo, Tô Gia Huy, Trương Mậu Điền, Trần Kỳ Đại, Nguyễn An Minh Trí]
- **Email:** [tanthaibao@gmail.com]
- **Ngày hoàn thành:** 08/05/2025

Nếu cần hỗ trợ thêm, vui lòng liên hệ qua email trên. Chúc bạn chạy đồ án thành công!
