<?php
echo "Admin: " . (password_verify('admin@123', '$2y$10$bcbQUmWnKWvgOxkjr/upQO.wsdNlhKZhAnD2whOhi.Y5k10l0f0MW') ? "OK" : "FAIL") . PHP_EOL;
echo "Seller: " . (password_verify('seller123', '$2y$10$t3z4Wl9d8Rf6h5.SE.JEdevEeKu0dQ8ERgPJVx4nON9pFqKfJC0dq') ? "OK" : "FAIL") . PHP_EOL;
echo "Customer: " . (password_verify('customer123', '$2y$10$rSsHge2HzsJQyWj1XW792eJmxeFEeUJUpsAStK.dl1tmNZNwxSgL6') ? "OK" : "FAIL") . PHP_EOL;
?>
