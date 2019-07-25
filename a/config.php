<?php

if (ENV == 'dev')     return include __DIR__ . DS .'config.dev.php';
if (ENV == 'product') return include __DIR__ . DS .'config.prod.php';
if (ENV == 'test')    return include __DIR__ . DS .'config.test.php';
