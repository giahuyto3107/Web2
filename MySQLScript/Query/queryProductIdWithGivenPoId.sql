use web2_sql;
select poi.product_id from purchase_order_items poi
join purchase_order po on po.purchase_order_id = poi.purchase_order_id
where po.import_status = 0 and poi.import_status = 0 and po.purchase_order_id = 1
