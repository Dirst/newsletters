<?php

require_once __DIR__.'/functions/queue.php';

while (true) {
    try {
        consume_for_send();
    } catch (\Exception $exception) {
        print $exception->getMessage().PHP_EOL;
        // Sleep a bit to decrease cpu usage on empty queue.
        sleep(5);
    }
}
