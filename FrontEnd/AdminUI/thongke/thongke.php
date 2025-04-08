<?php
// thongke.php

require_once '../../BackEnd/Config/database.php';

// Kiểm tra quyền truy cập (nếu cần)
// Ví dụ: Chỉ Quản trị viên (role_id = 1) và Quản lý (role_id = 4) được xem thống kê

// Lấy dữ liệu thống kê
$dailyRevenue = $db->getDailyRevenue();
$monthlyRevenue = $db->getMonthlyRevenue();
$topProducts = $db->getTopProducts();
$paymentStats = $db->getPaymentMethodStats();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Doanh Thu</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
            display: none; /* Ẩn tất cả biểu đồ ban đầu */
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        #viewType {
            margin: 10px 0;
            padding: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Thống Kê Doanh Thu</h1>
    <select id="viewType">
        <option value="month">Theo Tháng</option>
        <option value="day">Theo Ngày</option>
    </select>
    <div class="chart-container" id="monthlyChartContainer">
        <h2>Doanh Thu Theo Tháng</h2>
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-container" id="dailyChartContainer">
        <h2>Doanh Thu Theo Ngày</h2>
        <canvas id="dailyRevenueChart"></canvas>
    </div>
    <script>
        const monthlyRevenue = <?php echo json_encode($monthlyRevenue); ?>;
        const dailyRevenue = <?php echo json_encode($dailyRevenue); ?>;
        const monthlyLabels = monthlyRevenue.map(item => `${item.sale_month}/${item.sale_year}`);
        const monthlyRevenues = monthlyRevenue.map(item => item.monthly_revenue);
        const dailyLabels = dailyRevenue.map(item => item.sale_date);
        const dailyRevenues = dailyRevenue.map(item => item.daily_revenue);
        const ctxMonthly = document.getElementById('revenueChart').getContext('2d');
        const monthlyChart = new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Doanh Thu (VND)',
                    data: monthlyRevenues,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Doanh Thu (VND)' }
                    },
                    x: {
                        title: { display: true, text: 'Tháng/Năm' }
                    }
                }
            }
        });
        const ctxDaily = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyChart = new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Doanh Thu (VND)',
                    data: dailyRevenues,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Doanh Thu (VND)' }
                    },
                    x: {
                        title: { display: true, text: 'Ngày' }
                    }
                }
            }
        });
        document.getElementById('viewType').addEventListener('change', function() {
            const viewType = this.value;
            if (viewType === 'day') {
                document.getElementById('dailyChartContainer').style.display = 'block';
                document.getElementById('monthlyChartContainer').style.display = 'none';
            } else if (viewType === 'month') {
                document.getElementById('dailyChartContainer').style.display = 'none';
                document.getElementById('monthlyChartContainer').style.display = 'block';
            }
        });
        document.getElementById('viewType').value = 'month';
        document.getElementById('monthlyChartContainer').style.display = 'block';
    </script>
    <h2>Doanh Thu Theo Ngày</h2>
    <table id="dailyRevenueTable" class="display">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Doanh Thu (VND)</th>
                <th>Số Đơn Hàng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyRevenue as $day): ?>
            <tr>
                <td><?php echo htmlspecialchars($day['sale_date']); ?></td>
                <td><?php echo number_format($day['daily_revenue'], 0, ',', '.'); ?></td>
                <td><?php echo $day['total_orders']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $('#dailyRevenueTable').DataTable({
                "language": {
                    "processing":"Đang xử lý...","aria":{"sortAscending":": Sắp xếp thứ tự tăng dần","sortDescending":": Sắp xếp thứ tự giảm dần"},"autoFill":{"cancel":"Hủy","fill":"Điền tất cả ô với \u003Ci\u003E%d\u003C/i\u003E","fillHorizontal":"Điền theo hàng ngang","fillVertical":"Điền theo hàng dọc"},"buttons":{"collection":"Chọn lọc \u003Cspan class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"\u003E\u003C/span\u003E","colvis":"Hiển thị theo cột","colvisRestore":"Khôi phục hiển thị","copy":"Sao chép","copyKeys":"Nhấn Ctrl hoặc u2318 + C để sao chép bảng dữ liệu vào clipboard.\u003Cbr /\u003E\u003Cbr /\u003EĐể hủy, click vào thông báo này hoặc nhấn ESC","copySuccess":{"1":"Đã sao chép 1 dòng dữ liệu vào clipboard","_":"Đã sao chép %d dòng vào clipboard"},"copyTitle":"Sao chép vào clipboard","pageLength":{"-1":"Xem tất cả các dòng","_":"Hiển thị %d dòng","1":"Hiển thị 1 dòng"},"print":"In ấn","createState":"Tạo trạng thái","csv":"CSV","excel":"Excel","pdf":"PDF","removeAllStates":"Xóa hết trạng thái","removeState":"Xóa","renameState":"Đổi tên","savedStates":"Trạng thái đã lưu","stateRestore":"Trạng thái %d","updateState":"Cập nhật"},"select":{"cells":{"1":"1 ô đang được chọn","_":"%d ô đang được chọn"},"columns":{"1":"1 cột đang được chọn","_":"%d cột đang được được chọn"},"rows":{"1":"1 dòng đang được chọn","_":"%d dòng đang được chọn"}},"searchBuilder":{"title":{"_":"Thiết lập tìm kiếm (%d)","0":"Thiết lập tìm kiếm"},"button":{"0":"Thiết lập tìm kiếm","_":"Thiết lập tìm kiếm (%d)"},"value":"Giá trị","clearAll":"Xóa hết","condition":"Điều kiện","conditions":{"date":{"after":"Sau","before":"Trước","between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"number":{"between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","gt":"Lớn hơn","gte":"Lớn hơn hoặc bằng","lt":"Nhỏ hơn","lte":"Nhỏ hơn hoặc bằng","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"string":{"contains":"Chứa","empty":"Rỗng","endsWith":"Kết thúc bằng","equals":"Bằng","not":"Không phải","notEmpty":"Không rỗng","startsWith":"Bắt đầu với","notContains":"Không chứa","notEndsWith":"Không kết thúc với","notStartsWith":"Không bắt đầu với"},"array":{"equals":"Bằng","empty":"Trống","contains":"Chứa","not":"Không","notEmpty":"Không được rỗng","without":"không chứa"}},"logicAnd":"Và","logicOr":"Hoặc","add":"Thêm điều kiện","data":"Dữ liệu","deleteTitle":"Xóa quy tắc lọc","leftTitle":"Giảm thụt lề","rightTitle":"Tăng thụt lề"},"searchPanes":{"countFiltered":"{shown} ({total})","emptyPanes":"Không có phần tìm kiếm","clearMessage":"Xóa hết","loadMessage":"Đang load phần tìm kiếm","collapse":{"0":"Phần tìm kiếm","_":"Phần tìm kiếm (%d)"},"title":"Bộ lọc đang hoạt động - %d","count":"{total}","collapseMessage":"Thu gọn tất cả","showMessage":"Hiện tất cả"},"datetime":{"hours":"Giờ","minutes":"Phút","next":"Sau","previous":"Trước","seconds":"Giây","amPm":["am","pm"],"unknown":"-","weekdays":["Chủ nhật"],"months":["Tháng Một","Tháng Hai","Tháng Ba","Tháng Tư","Tháng Năm","Tháng Sáu","Tháng Bảy","Tháng Tám","Tháng Chín","Tháng Mười","Tháng Mười Một","Tháng Mười Hai"]},"emptyTable":"Không có dữ liệu","info":"Hiển thị _START_ tới _END_ của _TOTAL_ dữ liệu","infoEmpty":"Hiển thị 0 tới 0 của 0 dữ liệu","lengthMenu":"Hiển thị _MENU_ dữ liệu","loadingRecords":"Đang tải...","paginate":{"first":"Đầu tiên","last":"Cuối cùng","next":"Sau","previous":"Trước"},"search":"Tìm kiếm:","zeroRecords":"Không tìm thấy kết quả","decimal":",","editor":{"close":"Đóng","create":{"button":"Thêm","submit":"Thêm","title":"Thêm mục mới"},"edit":{"button":"Sửa","submit":"Cập nhật","title":"Sửa mục"},"error":{"system":"Đã xảy ra lỗi hệ thống (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Thêm thông tin</a>)."},"multi":{"info":"Các mục đã chọn chứa các giá trị khác nhau cho đầu vào này. Để chỉnh sửa và đặt tất cả các mục cho đầu vào này thành cùng một giá trị, hãy nhấp hoặc nhấn vào đây, nếu không chúng sẽ giữ lại các giá trị riêng lẻ của chúng.","noMulti":"Đầu vào này có thể được chỉnh sửa riêng lẻ, nhưng không phải là một phần của một nhóm.","restore":"Hoàn tác thay đổi","title":"Nhiều giá trị"},"remove":{"button":"Xóa","confirm":{"_":"Bạn có chắc chắn muốn xóa %d hàng không?","1":"Bạn có chắc chắn muốn xóa 1 hàng không?"},"submit":"Xóa","title":"Xóa"}},"infoFiltered":"(được lọc từ _MAX_ dữ liệu)","searchPlaceholder":"Nhập tìm kiếm...","stateRestore":{"creationModal":{"button":"Thêm","columns":{"search":"Tìm kiếm cột","visible":"Khả năng hiển thị cột"},"name":"Tên:","order":"Sắp xếp","paging":"Phân trang","scroller":"Cuộn vị trí","search":"Tìm kiếm","searchBuilder":"Trình tạo tìm kiếm","select":"Chọn","title":"Thêm trạng thái","toggleLabel":"Bao gồm:"},"duplicateError":"Trạng thái có tên này đã tồn tại.","emptyError":"Tên không được để trống.","emptyStates":"Không có trạng thái đã lưu","removeConfirm":"Bạn có chắc chắn muốn xóa %s không?","removeError":"Không xóa được trạng thái.","removeJoiner":"và","removeSubmit":"Xóa","removeTitle":"Xóa trạng thái","renameButton":"Đổi tên","renameLabel":"Tên mới cho %s:","renameTitle":"Đổi tên trạng thái"},"infoThousands":".","thousands":"."
                }
            });
        });
    </script>
    <h2>Top Sản Phẩm Bán Chạy</h2>
    <table id="topProductsTable" class="display">
        <thead>
            <tr>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng Bán</th>
                <th>Doanh Thu (VND)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topProducts as $productName => $data): ?>
            <tr>
                <td><?php echo htmlspecialchars($productName); ?></td>
                <td><?php echo $data['total_sold']; ?></td>
                <td><?php echo number_format($data['total_revenue'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $('#topProductsTable').DataTable({
                "language": {
                    "processing":"Đang xử lý...","aria":{"sortAscending":": Sắp xếp thứ tự tăng dần","sortDescending":": Sắp xếp thứ tự giảm dần"},"autoFill":{"cancel":"Hủy","fill":"Điền tất cả ô với \u003Ci\u003E%d\u003C/i\u003E","fillHorizontal":"Điền theo hàng ngang","fillVertical":"Điền theo hàng dọc"},"buttons":{"collection":"Chọn lọc \u003Cspan class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"\u003E\u003C/span\u003E","colvis":"Hiển thị theo cột","colvisRestore":"Khôi phục hiển thị","copy":"Sao chép","copyKeys":"Nhấn Ctrl hoặc u2318 + C để sao chép bảng dữ liệu vào clipboard.\u003Cbr /\u003E\u003Cbr /\u003EĐể hủy, click vào thông báo này hoặc nhấn ESC","copySuccess":{"1":"Đã sao chép 1 dòng dữ liệu vào clipboard","_":"Đã sao chép %d dòng vào clipboard"},"copyTitle":"Sao chép vào clipboard","pageLength":{"-1":"Xem tất cả các dòng","_":"Hiển thị %d dòng","1":"Hiển thị 1 dòng"},"print":"In ấn","createState":"Tạo trạng thái","csv":"CSV","excel":"Excel","pdf":"PDF","removeAllStates":"Xóa hết trạng thái","removeState":"Xóa","renameState":"Đổi tên","savedStates":"Trạng thái đã lưu","stateRestore":"Trạng thái %d","updateState":"Cập nhật"},"select":{"cells":{"1":"1 ô đang được chọn","_":"%d ô đang được chọn"},"columns":{"1":"1 cột đang được chọn","_":"%d cột đang được được chọn"},"rows":{"1":"1 dòng đang được chọn","_":"%d dòng đang được chọn"}},"searchBuilder":{"title":{"_":"Thiết lập tìm kiếm (%d)","0":"Thiết lập tìm kiếm"},"button":{"0":"Thiết lập tìm kiếm","_":"Thiết lập tìm kiếm (%d)"},"value":"Giá trị","clearAll":"Xóa hết","condition":"Điều kiện","conditions":{"date":{"after":"Sau","before":"Trước","between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"number":{"between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","gt":"Lớn hơn","gte":"Lớn hơn hoặc bằng","lt":"Nhỏ hơn","lte":"Nhỏ hơn hoặc bằng","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"string":{"contains":"Chứa","empty":"Rỗng","endsWith":"Kết thúc bằng","equals":"Bằng","not":"Không phải","notEmpty":"Không rỗng","startsWith":"Bắt đầu với","notContains":"Không chứa","notEndsWith":"Không kết thúc với","notStartsWith":"Không bắt đầu với"},"array":{"equals":"Bằng","empty":"Trống","contains":"Chứa","not":"Không","notEmpty":"Không được rỗng","without":"không chứa"}},"logicAnd":"Và","logicOr":"Hoặc","add":"Thêm điều kiện","data":"Dữ liệu","deleteTitle":"Xóa quy tắc lọc","leftTitle":"Giảm thụt lề","rightTitle":"Tăng thụt lề"},"searchPanes":{"countFiltered":"{shown} ({total})","emptyPanes":"Không có phần tìm kiếm","clearMessage":"Xóa hết","loadMessage":"Đang load phần tìm kiếm","collapse":{"0":"Phần tìm kiếm","_":"Phần tìm kiếm (%d)"},"title":"Bộ lọc đang hoạt động - %d","count":"{total}","collapseMessage":"Thu gọn tất cả","showMessage":"Hiện tất cả"},"datetime":{"hours":"Giờ","minutes":"Phút","next":"Sau","previous":"Trước","seconds":"Giây","amPm":["am","pm"],"unknown":"-","weekdays":["Chủ nhật"],"months":["Tháng Một","Tháng Hai","Tháng Ba","Tháng Tư","Tháng Năm","Tháng Sáu","Tháng Bảy","Tháng Tám","Tháng Chín","Tháng Mười","Tháng Mười Một","Tháng Mười Hai"]},"emptyTable":"Không có dữ liệu","info":"Hiển thị _START_ tới _END_ của _TOTAL_ dữ liệu","infoEmpty":"Hiển thị 0 tới 0 của 0 dữ liệu","lengthMenu":"Hiển thị _MENU_ dữ liệu","loadingRecords":"Đang tải...","paginate":{"first":"Đầu tiên","last":"Cuối cùng","next":"Sau","previous":"Trước"},"search":"Tìm kiếm:","zeroRecords":"Không tìm thấy kết quả","decimal":",","editor":{"close":"Đóng","create":{"button":"Thêm","submit":"Thêm","title":"Thêm mục mới"},"edit":{"button":"Sửa","submit":"Cập nhật","title":"Sửa mục"},"error":{"system":"Đã xảy ra lỗi hệ thống (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Thêm thông tin</a>)."},"multi":{"info":"Các mục đã chọn chứa các giá trị khác nhau cho đầu vào này. Để chỉnh sửa và đặt tất cả các mục cho đầu vào này thành cùng một giá trị, hãy nhấp hoặc nhấn vào đây, nếu không chúng sẽ giữ lại các giá trị riêng lẻ của chúng.","noMulti":"Đầu vào này có thể được chỉnh sửa riêng lẻ, nhưng không phải là một phần của một nhóm.","restore":"Hoàn tác thay đổi","title":"Nhiều giá trị"},"remove":{"button":"Xóa","confirm":{"_":"Bạn có chắc chắn muốn xóa %d hàng không?","1":"Bạn có chắc chắn muốn xóa 1 hàng không"},"submit":"Xóa","title":"Xóa"}},"infoFiltered":"(được lọc từ _MAX_ dữ liệu)","searchPlaceholder":"Nhập tìm kiếm...","stateRestore":{"creationModal":{"button":"Thêm","columns":{"search":"Tìm kiếm cột","visible":"Khả năng hiển thị cột"},"name":"Tên:","order":"Sắp xếp","paging":"Phân trang","scroller":"Cuộn vị trí","search":"Tìm kiếm","searchBuilder":"Trình tạo tìm kiếm","select":"Chọn","title":"Thêm trạng thái","toggleLabel":"Bao gồm:"},"duplicateError":"Trạng thái có tên này đã tồn tại.","emptyError":"Tên không được để trống.","emptyStates":"Không có trạng thái đã lưu","removeConfirm":"Bạn có chắc chắn muốn xóa %s không?","removeError":"Không xóa được trạng thái.","removeJoiner":"và","removeSubmit":"Xóa","removeTitle":"Xóa trạng thái","renameButton":"Đổi tên","renameLabel":"Tên mới cho %s:","renameTitle":"Đổi tên trạng thái"},"infoThousands":".","thousands":"."
                }
            });
        });
    </script>
    <h2>Phân Tích Phương Thức Thanh Toán</h2>
    <table id="paymentStatsTable" class="display">
        <thead>
            <tr>
                <th>Phương Thức Thanh Toán</th>
                <th>Số Đơn Hàng</th>
                <th>Doanh Thu (VND)</th>
                <th>Giá Trị Trung Bình (VND)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paymentStats as $stat): ?>
            <tr>
                <td><?php echo htmlspecialchars($stat['payment_method']); ?></td>
                <td><?php echo $stat['order_count']; ?></td>
                <td><?php echo number_format($stat['total_revenue'], 0, ',', '.'); ?></td>
                <td><?php echo number_format($stat['avg_order_value'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $('#paymentStatsTable').DataTable({
                "language": {
                    "processing":"Đang xử lý...","aria":{"sortAscending":": Sắp xếp thứ tự tăng dần","sortDescending":": Sắp xếp thứ tự giảm dần"},"autoFill":{"cancel":"Hủy","fill":"Điền tất cả ô với \u003Ci\u003E%d\u003C/i\u003E","fillHorizontal":"Điền theo hàng ngang","fillVertical":"Điền theo hàng dọc"},"buttons":{"collection":"Chọn lọc \u003Cspan class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"\u003E\u003C/span\u003E","colvis":"Hiển thị theo cột","colvisRestore":"Khôi phục hiển thị","copy":"Sao chép","copyKeys":"Nhấn Ctrl hoặc u2318 + C để sao chép bảng dữ liệu vào clipboard.\u003Cbr /\u003E\u003Cbr /\u003EĐể hủy, click vào thông báo này hoặc nhấn ESC","copySuccess":{"1":"Đã sao chép 1 dòng dữ liệu vào clipboard","_":"Đã sao chép %d dòng vào clipboard"},"copyTitle":"Sao chép vào clipboard","pageLength":{"-1":"Xem tất cả các dòng","_":"Hiển thị %d dòng","1":"Hiển thị 1 dòng"},"print":"In ấn","createState":"Tạo trạng thái","csv":"CSV","excel":"Excel","pdf":"PDF","removeAllStates":"Xóa hết trạng thái","removeState":"Xóa","renameState":"Đổi tên","savedStates":"Trạng thái đã lưu","stateRestore":"Trạng thái %d","updateState":"Cập nhật"},"select":{"cells":{"1":"1 ô đang được chọn","_":"%d ô đang được chọn"},"columns":{"1":"1 cột đang được chọn","_":"%d cột đang được được chọn"},"rows":{"1":"1 dòng đang được chọn","_":"%d dòng đang được chọn"}},"searchBuilder":{"title":{"_":"Thiết lập tìm kiếm (%d)","0":"Thiết lập tìm kiếm"},"button":{"0":"Thiết lập tìm kiếm","_":"Thiết lập tìm kiếm (%d)"},"value":"Giá trị","clearAll":"Xóa hết","condition":"Điều kiện","conditions":{"date":{"after":"Sau","before":"Trước","between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"number":{"between":"Nằm giữa","empty":"Rỗng","equals":"Bằng với","gt":"Lớn hơn","gte":"Lớn hơn hoặc bằng","lt":"Nhỏ hơn","lte":"Nhỏ hơn hoặc bằng","not":"Không phải","notBetween":"Không nằm giữa","notEmpty":"Không rỗng"},"string":{"contains":"Chứa","empty":"Rỗng","endsWith":"Kết thúc bằng","equals":"Bằng","not":"Không phải","notEmpty":"Không rỗng","startsWith":"Bắt đầu với","notContains":"Không chứa","notEndsWith":"Không kết thúc với","notStartsWith":"Không bắt đầu với"},"array":{"equals":"Bằng","empty":"Trống","contains":"Chứa","not":"Không","notEmpty":"Không được rỗng","without":"không chứa"}},"logicAnd":"Và","logicOr":"Hoặc","add":"Thêm điều kiện","data":"Dữ liệu","deleteTitle":"Xóa quy tắc lọc","leftTitle":"Giảm thụt lề","rightTitle":"Tăng thụt lề"},"searchPanes":{"countFiltered":"{shown} ({total})","emptyPanes":"Không có phần tìm kiếm","clearMessage":"Xóa hết","loadMessage":"Đang load phần tìm kiếm","collapse":{"0":"Phần tìm kiếm","_":"Phần tìm kiếm (%d)"},"title":"Bộ lọc đang hoạt động - %d","count":"{total}","collapseMessage":"Thu gọn tất cả","showMessage":"Hiện tất cả"},"datetime":{"hours":"Giờ","minutes":"Phút","next":"Sau","previous":"Trước","seconds":"Giây","amPm":["am","pm"],"unknown":"-","weekdays":["Chủ nhật"],"months":["Tháng Một","Tháng Hai","Tháng Ba","Tháng Tư","Tháng Năm","Tháng Sáu","Tháng Bảy","Tháng Tám","Tháng Chín","Tháng Mười","Tháng Mười Một","Tháng Mười Hai"]},"emptyTable":"Không có dữ liệu","info":"Hiển thị _START_ tới _END_ của _TOTAL_ dữ liệu","infoEmpty":"Hiển thị 0 tới 0 của 0 dữ liệu","lengthMenu":"Hiển thị _MENU_ dữ liệu","loadingRecords":"Đang tải...","paginate":{"first":"Đầu tiên","last":"Cuối cùng","next":"Sau","previous":"Trước"},"search":"Tìm kiếm:","zeroRecords":"Không tìm thấy kết quả","decimal":",","editor":{"close":"Đóng","create":{"button":"Thêm","submit":"Thêm","title":"Thêm mục mới"},"edit":{"button":"Sửa","submit":"Cập nhật","title":"Sửa mục"},"error":{"system":"Đã xảy ra lỗi hệ thống (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Thêm thông tin</a>)."},"multi":{"info":"Các mục đã chọn chứa các giá trị khác nhau cho đầu vào này. Để chỉnh sửa và đặt tất cả các mục cho đầu vào này thành cùng một giá trị, hãy nhấp hoặc nhấn vào đây, nếu không chúng sẽ giữ lại các giá trị riêng lẻ của chúng.","noMulti":"Đầu vào này có thể được chỉnh sửa riêng lẻ, nhưng không phải là một phần của một nhóm.","restore":"Hoàn tác thay đổi","title":"Nhiều giá trị"},"remove":{"button":"Xóa","confirm":{"_":"Bạn có chắc chắn muốn xóa %d hàng không?","1":"Bạn có chắc chắn muốn xóa 1 hàng không"},"submit":"Xóa","title":"Xóa"}},"infoFiltered":"(được lọc từ _MAX_ dữ liệu)","searchPlaceholder":"Nhập tìm kiếm...","stateRestore":{"creationModal":{"button":"Thêm","columns":{"search":"Tìm kiếm cột","visible":"Khả năng hiển thị cột"},"name":"Tên:","order":"Sắp xếp","paging":"Phân trang","scroller":"Cuộn vị trí","search":"Tìm kiếm","searchBuilder":"Trình tạo tìm kiếm","select":"Chọn","title":"Thêm trạng thái","toggleLabel":"Bao gồm:"},"duplicateError":"Trạng thái có tên này đã tồn tại.","emptyError":"Tên không được để trống.","emptyStates":"Không có trạng thái đã lưu","removeConfirm":"Bạn có chắc chắn muốn xóa %s không?","removeError":"Không xóa được trạng thái.","removeJoiner":"và","removeSubmit":"Xóa","removeTitle":"Xóa trạng thái","renameButton":"Đổi tên","renameLabel":"Tên mới cho %s:","renameTitle":"Đổi tên trạng thái"},"infoThousands":".","thousands":"."
                }
            });
        });
    </script>
</body>
</html>