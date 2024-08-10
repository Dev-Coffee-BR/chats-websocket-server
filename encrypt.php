<?php

$iv = '1234567890123456';
$key = '1234567890123456';
$token = openssl_encrypt("password", "aes-128-cbc", "marrios", 0, "1234567890123456");
echo openssl_decrypt("KQwHu0KtbLSvBhEY554MbpixsrLJOdTnKKhr08cAWfJsQ+bP2ogU7ZOfPlWoU7roilf5Hj3TL8ZeaeG9Nsub0Ug8Nc8gOTmJP7YiHAufKoDWs+tqRmL9z+l03M/ryvQ/", "aes-128-cbc", $key, 0, $iv);