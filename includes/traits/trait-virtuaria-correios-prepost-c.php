<?php
 defined("\x41\x42\x53\x50\101\124\x48") || die; trait Virtuaria_Correios_PrePost_Functions { private function get_formatted_prepost($order, $shipping) { $customer_phone = $order->get_meta("\137\x62\151\x6c\154\x69\156\x67\x5f\143\145\154\x6c\160\x68\157\156\x65") ? $order->get_meta("\137\x62\151\x6c\154\151\x6e\147\137\x63\x65\x6c\154\x70\150\x6f\156\145") : $order->get_billing_phone(); if (!empty($customer_phone)) { $customer_phone = str_replace("\x2b\65\x35", '', $customer_phone); $customer_phone = preg_replace("\57\134\104\57", '', $customer_phone); } $ddd = substr($customer_phone, 0, 2); $tel = substr($customer_phone, 2); $packages = array(); $items = array(); foreach ($order->get_items() as $item) { $product = $item->get_product(); if (!$product) { continue; } $packages["\x63\157\156\164\x65\156\164\x73"][] = array("\x64\141\x74\141" => $product, "\161\x75\141\156\x74\x69\164\171" => $item->get_quantity()); $items[] = array("\143\x6f\x6e\164\145\x75\x64\x6f" => $product->get_title(), "\161\165\x61\156\164\x69\144\141\x64\145" => strval($item->get_quantity()), "\x76\141\x6c\157\x72" => $item->get_total()); } $dimensions = $shipping->get_package_dimensions($packages); $additional_services = array(); if ("\171\145\x73" === $shipping->get_option("\x72\145\x63\145\151\160\x74\x5f\x6e\x6f\164\151\x63\x65")) { $additional_services[] = array("\143\157\x64\x69\x67\157\123\x65\x72\166\x69\143\157\101\144\x69\x63\x69\x6f\156\x61\154" => "\x30\x30\61", "\166\x61\154\x6f\x72\104\145\x63\154\141\x72\x61\x64\x6f" => $order->get_total()); } if ("\171\145\x73" === $shipping->get_option("\x6f\167\156\137\150\141\x6e\144\163")) { $additional_services[] = array("\x63\157\x64\151\147\x6f\123\x65\162\166\x69\x63\x6f\x41\144\151\143\x69\x6f\x6e\141\x6c" => "\60\x30\62", "\166\x61\154\x6f\162\104\145\143\154\x61\x72\141\x64\157" => $order->get_total()); } if ($shipping->get_option("\x64\145\x63\x6c\141\x72\145\x5f\166\141\x6c\165\145")) { $additional_services[] = array("\x63\x6f\144\x69\147\157\x53\x65\x72\166\151\143\x6f\101\x64\151\143\151\157\156\x61\154" => $shipping->get_option("\144\x65\x63\x6c\x61\162\145\137\166\x61\x6c\165\x65"), "\166\141\154\x6f\x72\104\145\x63\154\x61\x72\x61\144\x6f" => $order->get_total()); } $prepost = array("\x72\x65\155\145\164\x65\x6e\164\x65" => array("\x6e\157\155\145" => $shipping->get_setting("\146\165\x6c\154\x5f\156\x61\155\x65"), "\145\x6d\141\x69\x6c" => $shipping->get_setting("\x65\155\x61\x69\x6c"), "\144\144\x64\103\x65\x6c\165\154\141\x72" => $shipping->get_setting("\x64\144\144"), "\143\x65\x6c\x75\x6c\x61\162" => $shipping->get_setting("\x66\x6f\x6e\145"), "\x63\x70\146\103\x6e\160\x6a" => $shipping->get_setting("\143\x70\x66\x63\x6e\160\152"), "\x65\x6e\144\x65\162\145\x63\157" => array("\x63\145\x70" => $shipping->get_setting("\x6f\162\151\x67\151\156"), "\x6c\x6f\x67\162\x61\x64\157\x75\162\157" => $shipping->get_setting("\154\x6f\147\162\141\144\x6f\165\x72\157"), "\156\x75\155\145\162\157" => $shipping->get_setting("\156\165\155\x65\162\157"), "\x62\x61\151\162\x72\x6f" => $shipping->get_setting("\x62\x61\x69\162\162\x6f"), "\143\151\x64\141\x64\145" => $shipping->get_setting("\143\151\x64\x61\x64\145"), "\x75\x66" => $shipping->get_setting("\x65\x73\x74\x61\144\x6f"))), "\144\x65\x73\x74\x69\x6e\141\x74\x61\162\x69\157" => array("\156\157\155\x65" => trim($order->get_formatted_shipping_full_name()) ? $order->get_formatted_shipping_full_name() : $order->get_formatted_billing_full_name(), "\145\x6d\x61\151\x6c" => $order->get_billing_email(), "\x64\144\x64\x43\x65\154\x75\x6c\x61\x72" => $ddd, "\x63\x65\154\x75\154\x61\162" => $tel, "\x63\160\x66\103\156\x70\x6a" => preg_replace("\x2f\x5c\x44\57", '', $order->get_meta("\137\142\x69\x6c\154\x69\156\147\137\x63\x70\x66")), "\x65\x6e\144\145\162\x65\143\x6f" => array("\143\145\x70" => $order->get_shipping_postcode() ? preg_replace("\57\x5c\x44\x2f", '', $order->get_shipping_postcode()) : preg_replace("\x2f\x5c\104\x2f", '', $order->get_billing_postcode()), "\154\x6f\147\x72\x61\144\157\x75\162\x6f" => $order->get_shipping_postcode() ? substr($order->get_shipping_address_1(), 0, 50) : substr($order->get_billing_address_1(), 0, 50), "\x6e\x75\155\x65\162\x6f" => $order->get_shipping_postcode() ? $order->get_meta("\137\163\x68\151\x70\x70\x69\x6e\x67\x5f\156\165\x6d\x62\145\x72") : $order->get_meta("\x5f\x62\151\154\x6c\151\x6e\x67\137\156\x75\155\142\x65\x72"), "\142\x61\151\x72\x72\x6f" => $order->get_shipping_postcode() ? substr($order->get_meta("\137\x73\x68\151\160\160\151\156\147\137\x6e\145\x69\x67\x68\142\x6f\x72\150\157\x6f\x64"), 0, 29) : substr($order->get_meta("\137\x62\151\x6c\x6c\x69\156\147\137\156\145\151\x67\150\x62\157\162\150\x6f\157\x64"), 0, 29), "\x63\x69\144\x61\144\x65" => $order->get_shipping_postcode() ? substr($order->get_shipping_city(), 0, 29) : substr($order->get_billing_city(), 0, 29), "\x75\146" => $order->get_shipping_postcode() ? $order->get_shipping_state() : $order->get_billing_state())), "\143\157\x64\x69\147\x6f\x53\x65\x72\x76\x69\x63\157" => $shipping->get_option("\x73\x65\162\x76\x69\x63\x65\x5f\x63\157\144"), "\143\x69\x65\x6e\x74\x65\117\142\x6a\x65\x74\x6f\x4e\141\157\120\x72\x6f\x69\142\151\x64\x6f" => "\x31", "\x6d\x6f\144\141\154\151\x64\141\x64\x65\120\x61\x67\141\155\x65\x6e\164\157" => "\62", "\154\x6f\x67\x69\x73\x74\x69\x63\x61\122\145\x76\x65\x72\x73\x61" => "\116", "\160\145\x73\157\x49\156\146\157\x72\x6d\141\x64\157" => isset($dimensions["\167\145\151\x67\x68\164"]) ? strval($dimensions["\167\145\x69\x67\150\164"]) : "\x30", "\x61\x6c\x74\x75\162\141\111\x6e\x66\157\162\x6d\x61\x64\141" => isset($dimensions["\150\145\151\147\x68\x74"]) ? $dimensions["\x68\x65\x69\147\x68\164"] : "\x30", "\x6c\x61\x72\x67\x75\x72\141\x49\156\146\157\162\155\x61\144\141" => isset($dimensions["\167\151\144\x74\x68"]) ? $dimensions["\x77\x69\x64\164\x68"] : "\x30", "\x63\x6f\x6d\160\162\x69\x6d\x65\156\x74\x6f\111\156\146\x6f\x72\155\141\144\157" => isset($dimensions["\154\145\156\147\x74\150"]) ? $dimensions["\x6c\x65\x6e\x67\164\150"] : "\60", "\x69\x74\x65\156\x73\104\x65\x63\x6c\x61\162\141\143\141\x6f\x43\x6f\x6e\164\145\x75\144\157" => $items, "\154\151\163\164\141\x53\145\x72\166\x69\143\157\101\x64\x69\x63\x69\x6f\156\x61\154" => $additional_services, "\165\163\x65\162\156\141\x6d\x65" => $shipping->username, "\160\141\x73\x73\x77\157\x72\144" => $shipping->password, "\x70\157\163\164\x5f\143\x61\x72\144" => $shipping->post_card, "\x63\157\144\151\147\157\106\157\x72\155\141\164\x6f\117\142\x6a\145\x74\x6f\x49\156\x66\157\162\x6d\141\x64\x6f" => $shipping->get_option("\157\x62\x6a\145\143\x74\137\164\x79\x70\145", "\x32"), "\163\157\x6c\151\x63\151\x74\x61\162\x43\x6f\x6c\145\x74\141" => "\116", "\151\144\x43\157\x72\x72\x65\151\x6f\x73" => $shipping->username); $origin_complement = $shipping->get_setting("\143\157\155\x70\x6c\145\x6d\145\x6e\164\157"); if ($origin_complement) { $prepost["\162\145\155\145\x74\x65\156\164\145"]["\x65\x6e\144\145\x72\x65\x63\x6f"]["\143\x6f\155\160\x6c\145\155\x65\x6e\x74\x6f"] = substr($origin_complement, 0, 29); } $recipient_complement = $order->get_shipping_postcode() ? $order->get_shipping_address_2() : $order->get_billing_address_2(); if ($recipient_complement) { $prepost["\144\145\x73\x74\x69\x6e\141\x74\141\162\151\x6f"]["\x65\x6e\144\x65\x72\145\x63\157"]["\x63\x6f\155\160\154\145\x6d\145\156\164\157"] = substr($recipient_complement, 0, 29); } if (isset($_POST["\x63\162\x65\x61\164\145\x5f\160\x72\x65\160\157\163\x74\137\156\157\x6e\x63\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x63\162\x65\141\x74\145\137\x70\162\145\160\157\163\164\x5f\x6e\x6f\156\x63\145"])), "\143\162\145\x61\164\145\x5f\x70\x72\145\160\x6f\x73\164")) { if (isset($_POST["\156\x66\x5f\153\x65\171"]) && !empty($_POST["\x6e\146\137\x6b\x65\x79"])) { $prepost["\x63\150\141\x76\x65\x4e\106\145"] = sanitize_text_field(wp_unslash($_POST["\x6e\x66\137\x6b\145\171"])); unset($prepost["\x69\164\x65\156\163\x44\145\x63\x6c\x61\162\x61\x63\141\157\x43\x6f\156\x74\145\165\x64\157"]); } if (isset($_POST["\x6e\146\x5f\156\x75\x6d\x62\145\162"]) && !empty($_POST["\x6e\146\x5f\156\165\x6d\142\x65\162"])) { $prepost["\156\x75\155\x65\x72\157\116\157\x74\x61\x46\151\x73\x63\x61\154"] = sanitize_text_field(wp_unslash($_POST["\x6e\x66\x5f\156\x75\155\x62\x65\162"])); unset($prepost["\x69\164\145\156\x73\104\x65\x63\154\x61\x72\x61\143\141\x6f\x43\x6f\156\x74\145\165\x64\157"]); } } if ("\62\60\61\71\x32" === $prepost["\143\x6f\x64\x69\x67\157\x53\x65\x72\166\x69\143\157"] || "\x31" === $shipping->get_option("\157\x62\x6a\145\143\164\137\164\171\x70\x65", "\x32")) { unset($prepost["\143\157\155\160\x72\x69\x6d\145\x6e\x74\x6f\x49\x6e\x66\157\x72\x6d\141\x64\x6f"]); unset($prepost["\141\154\164\165\162\141\x49\x6e\x66\157\x72\x6d\141\144\141"]); unset($prepost["\x6c\141\x72\147\165\162\141\111\x6e\x66\157\x72\155\141\144\x61"]); } $services_with_date_preview = apply_filters("\x76\151\162\164\165\x61\162\x69\x61\137\x63\157\x72\x72\x65\151\x6f\x73\x5f\x70\162\145\x70\x6f\x73\164\137\163\x65\162\166\x69\x63\145\x73\x5f\x77\151\x74\x68\137\144\x61\x74\145\x5f\x70\x72\x65\166\151\145\x77", array(80160, 80250, 80390, 80152)); if (in_array($prepost["\143\157\144\151\x67\157\x53\x65\x72\166\x69\x63\157"], $services_with_date_preview)) { $prepost["\x64\x61\x74\141\x50\162\x65\x76\x69\163\x74\x61\120\157\163\x74\141\x67\145\x6d"] = wp_date("\144\57\155\x2f\131", strtotime("\53\61\40\x64\141\x79")); } return $prepost; } }