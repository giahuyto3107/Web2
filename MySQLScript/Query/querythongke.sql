use web2_sql;
CREATE OR REPLACE VIEW sales_report AS
SELECT 
    o.order_id,
    o.order_date,
    DATE(o.order_date) AS sale_date,
    u.user_id,
    u.full_name AS customer_name,
    a.email AS customer_email,
    o.total_amount,
    o.payment_method,
    o.phone,
    o.address,
    s.status_name AS order_status,
    COUNT(oi.order_item_id) AS total_items,
    SUM(oi.quantity) AS total_quantity,
    -- Thông tin chi tiết sản phẩm (dạng JSON)
    (
        SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'product_id', p.product_id,
                'product_name', p.product_name,
                'quantity', oi2.quantity,
                'unit_price', oi2.price,
                'subtotal', oi2.price * oi2.quantity,
                'categories', (
                    SELECT JSON_ARRAYAGG(c.category_name)
                    FROM product_category pc
                    JOIN category c ON pc.category_id = c.category_id
                    WHERE pc.product_id = p.product_id
                )
            )
        )
        FROM order_items oi2
        JOIN product p ON oi2.product_id = p.product_id
        WHERE oi2.order_id = o.order_id
    ) AS product_details,
    -- Thông tin nhân viên xử lý (nếu có)
    u_admin.full_name AS admin_handler,
    -- Phân loại theo thời gian
    YEAR(o.order_date) AS sale_year,
    MONTH(o.order_date) AS sale_month,
    DAY(o.order_date) AS sale_day,
    QUARTER(o.order_date) AS sale_quarter,
    -- Phân tích trạng thái
    CASE 
        WHEN o.status_id = 5 THEN 'Completed'
        WHEN o.status_id = 4 THEN 'Shipping'
        WHEN o.status_id = 3 THEN 'Pending'
        WHEN o.status_id = 7 THEN 'Cancelled'
        ELSE 'Other'
    END AS status_category
FROM 
    orders o
JOIN 
    user u ON o.user_id = u.user_id
JOIN 
    account a ON u.account_id = a.account_id
JOIN 
    status s ON o.status_id = s.id
LEFT JOIN 
    user u_admin ON o.user_admin_id = u_admin.user_id
LEFT JOIN 
    order_items oi ON o.order_id = oi.order_id
GROUP BY 
    o.order_id, o.order_date, u.user_id, u.full_name, a.email, 
    o.total_amount, o.payment_method, o.phone, o.address, 
    s.status_name, u_admin.full_name;
    
    
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