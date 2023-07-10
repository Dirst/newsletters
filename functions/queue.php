<?php

require_once __DIR__.'/pdo_init.php';
require_once __DIR__.'/external_operations.php';

define('FROM_EMAIL', 'robot@test.com');

/**
 * Simple copy operation to put users with subscription and confirmed checkbox to send_queue table.
 * All users who have expiring subscription in 3 days, and all who have expiring one in 1 day.
 */
function enqueue_expiring_users(PDO $pdo): void
{
    $insertExpiringUsersSql = "INSERT INTO send_queue (username, email)
        SELECT u.username, u.email
        FROM users u
        WHERE u.confirmed = true AND (
          (u.validts <= UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 3 DAY)) AND u.validts > UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 2 DAY))) OR 
          (u.validts <= UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL 1 DAY)) AND u.validts > UNIX_TIMESTAMP(NOW()))
        );";

    $pdo->query($insertExpiringUsersSql);
}

function dequeue_item(PDO $pdo): StdClass
{
    $pdo->beginTransaction();
    $statement = $pdo->query(
        "select * from send_queue where process_id is null order by read_time asc, id asc limit 1 for update skip locked"
    );
    $queueItem = $statement->fetchObject();

    if ($queueItem === false) {
        $pdo->commit();
        throw new \Exception("No items to consume.");
    }

    $statement->closeCursor();

    $statement = $pdo->prepare('update send_queue set process_id = :process, read_time = UNIX_TIMESTAMP(NOW()) where id = :id');
    $statement->execute([':process' => getmypid(), ':id' => $queueItem->id]);
    $pdo->commit();

    return $queueItem;
}

function unlock_queue_item(PDO $pdo, int $itemId): void
{
    $statement = $pdo->prepare('update send_queue set process_id = null where id = :id');
    $statement->execute([':id' => $itemId]);
}

function ack_item_by_id(PDO $pdo, int $itemId): void
{
    $statement = $pdo->prepare('delete from send_queue where id = :id');
    $statement->execute([':id' => $itemId]);
}

function try_email_send(PDO $pdo, StdClass $queueItem): void
{
    try {
        $userName = $queueItem->username;
        $userEmail = $queueItem->email;
        $text = "$userName, your subscription is expiring soon";
        send_email(FROM_EMAIL, $userEmail, $text);
    } catch (\Exception $exception) {
        // In case of error unlock queue.
        unlock_queue_item($pdo, $queueItem->id);
        throw $exception;
    }
}

function consume_for_send(): void
{
    $pdo = get_pdo_connection();

    $queueItem = dequeue_item($pdo);
    try_email_send($pdo, $queueItem);
    ack_item_by_id($pdo, $queueItem->id);

    $pdo = null;
}