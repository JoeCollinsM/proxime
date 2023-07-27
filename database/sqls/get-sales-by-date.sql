DATE(orders.created_at) as                                                                   day,
       COUNT(*) as                                                                      			total_orders,
       SUM(orders.discount) as                                                                      total_discount,
       SUM((SELECT SUM(quantity)
            FROM `line_items`
            WHERE line_items.order_id = orders.id)) as                                              total_item_count,
       SUM((SELECT SUM(quantity * (price + tax))
            FROM `line_items`
            WHERE line_items.order_id = orders.id)) as                                              total_net_amount,
       SUM((SELECT SUM(quantity * tax) FROM `line_items` WHERE line_items.order_id = orders.id)) as total_tax_amount,
       SUM((SELECT SUM(quantity * (price + tax)) FROM `line_items` WHERE line_items.order_id = orders.id) +
           orders.shipping_charge - orders.discount) as                                             total_gross_amount
