<?php

if (ENV == 'dev')     return include __DIR__ . '/database.dev.php';
if (ENV == 'product') return include __DIR__ . '/database.prod.php';
if (ENV == 'test')    return include __DIR__ . '/database.test.php';
