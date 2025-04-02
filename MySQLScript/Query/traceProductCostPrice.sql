use web2_sql;
select poi.price as 'Gia nhap', (1 + poi.profit/100.0) * poi.price as 'Gia ban'
from product p
join purchase_order_items poi on poi.product_id = p.product_id
where p.product_id = 2 and poi.approve_date is not NULL
order by poi.approve_date desc
limit 1