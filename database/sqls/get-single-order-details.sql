       *,
       (SELECT COUNT(*) FROM `line_items` WHERE line_items.order_id = orders.id)                      as item_count,
       (SELECT SUM(quantity * (price + tax)) FROM `line_items` WHERE line_items.order_id = orders.id) as net_amount,
       (SELECT SUM(quantity * tax) FROM `line_items` WHERE line_items.order_id = orders.id)           as tax_amount,
       (SELECT SUM(quantity * (price + tax)) FROM `line_items` WHERE line_items.order_id = orders.id) +
       orders.shipping_charge - orders.discount                                                       as gross_amount
