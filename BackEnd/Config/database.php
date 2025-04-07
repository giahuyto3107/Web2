<?php
require_once 'pdo.php';
class Database {
    private $conn;
    public function __construct() {
        global $pdo; // Sử dụng biến $pdo từ pdo.php
        $this->conn = $pdo;
    }
    public function getConnection() {
        return $this->conn;
    }
    public function closeConnection() {
        $this->conn = null;
    }
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
public function getDailyRevenue() {
    try {
        $query = "
            SELECT 
                sale_date,
                SUM(total_amount) AS daily_revenue,
                COUNT(order_id) AS total_orders
            FROM 
                sales_report
            WHERE 
                status_category = 'Completed'
            GROUP BY 
                sale_date
            ORDER BY 
                sale_date DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Lỗi truy vấn getDailyRevenue: " . $e->getMessage());
        return [];
    }
}
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
            uasort($productSales, function($a, $b) {
                return $b['total_sold'] - $a['total_sold'];
            });
            return array_slice($productSales, 0, $limit);
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn getTopProducts: " . $e->getMessage());
            return [];
        }
    }
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
$db = new Database();
$conn = $db->getConnection();
?>