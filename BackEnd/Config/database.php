<?php
// database.php

require_once 'pdo.php'; // Include file kết nối PDO

class Database {
    private $conn;

    public function __construct() {
        global $pdo; // Sử dụng biến $pdo từ pdo.php
        $this->conn = $pdo;
    }

    // Trả về kết nối
    public function getConnection() {
        return $this->conn;
    }

    // Đóng kết nối (tùy chọn, PDO tự động đóng khi script kết thúc)
    public function closeConnection() {
        $this->conn = null;
    }

    // Phương thức lấy doanh thu theo tháng
    public function getMonthlyRevenue() {
        try {
            $query = "
                SELECT 
                    sale_year,
                    sale_month,
                    SUM(total_amount) AS monthly_revenue,
                    COUNT(order_id) AS total_orders
                FROM 
                    sales_report
                WHERE 
                    status_category = 'Completed'
                GROUP BY 
                    sale_year, sale_month
                ORDER BY 
                    sale_year DESC, sale_month DESC
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn getMonthlyRevenue: " . $e->getMessage());
            return [];
        }
    }

    // Phương thức lấy top sản phẩm bán chạy
    public function getTopProducts($limit = 10) {
        try {
            $query = "
                SELECT 
                    order_id,
                    product_details
                FROM 
                    sales_report
                WHERE 
                    status_category = 'Completed'
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $productSales = [];
            foreach ($results as $row) {
                $products = json_decode($row['product_details'], true);
                if (is_array($products)) {
                    foreach ($products as $product) {
                        $name = $product['product_name'];
                        $quantity = $product['quantity'];
                        $subtotal = $product['subtotal'];
                        if (!isset($productSales[$name])) {
                            $productSales[$name] = ['total_sold' => 0, 'total_revenue' => 0];
                        }
                        $productSales[$name]['total_sold'] += $quantity;
                        $productSales[$name]['total_revenue'] += $subtotal;
                    }
                }
            }

            // Sắp xếp theo số lượng bán được
            uasort($productSales, function($a, $b) {
                return $b['total_sold'] - $a['total_sold'];
            });

            // Lấy top $limit sản phẩm
            return array_slice($productSales, 0, $limit);
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn getTopProducts: " . $e->getMessage());
            return [];
        }
    }

    // Phương thức phân tích phương thức thanh toán
    public function getPaymentMethodStats() {
        try {
            $query = "
                SELECT 
                    payment_method,
                    COUNT(order_id) AS order_count,
                    SUM(total_amount) AS total_revenue,
                    AVG(total_amount) AS avg_order_value
                FROM 
                    sales_report
                WHERE 
                    status_category = 'Completed'
                GROUP BY 
                    payment_method
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn getPaymentMethodStats: " . $e->getMessage());
            return [];
        }
    }
}

// Khởi tạo đối tượng Database
$db = new Database();
$conn = $db->getConnection();
?>