<?php

require_once __DIR__.'/functions/queue.php';

enqueue_expiring_users(get_pdo_connection());



