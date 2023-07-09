<?php

function send_email(string $from, string $to, string $text): void
{
    $sleepSec = rand(1, 10);
    sleep($sleepSec);
}

function check_email(string $email): void
{
    $sleepSec = rand(1, 60);
    sleep($sleepSec);
}