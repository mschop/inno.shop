<?php

use function Functional\map;

_extend('body_content', fn($parent) => _ul(
   map($products, fn (string $product) => _li($product))
));

return _include('base.html.php');
