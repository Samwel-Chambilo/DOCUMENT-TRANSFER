<?php
require 'config.php';
$pdo->exec("UPDATE documents SET is_read = 1 WHERE is_read = 0");