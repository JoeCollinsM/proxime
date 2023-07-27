products.title,
(SELECT SUM(line_items.quantity) FROM line_items WHERE line_items.product_id=products.id) as item_count,
(SELECT SUM((line_items.quantity*(line_items.price+line_items.tax))) FROM line_items WHERE line_items.product_id=products.id) as net_total,
(SELECT SUM((line_items.quantity*line_items.tax)) FROM line_items WHERE line_items.product_id=products.id) as tax_total
