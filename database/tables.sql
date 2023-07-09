CREATE TABLE users
(
    id        INT UNSIGNED AUTO_INCREMENT,
    username  VARCHAR(255),
    email     VARCHAR(255),
    validts   INT UNSIGNED,
    confirmed BOOLEAN,
    checked   BOOLEAN,
    valid     BOOLEAN,
    PRIMARY KEY (id)
);

ALTER TABLE users ADD INDEX idx_confirmed_validts (confirmed, validts);

CREATE TABLE send_queue
(
    id         INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    username   VARCHAR(255),
    email      VARCHAR(255),
    read_time  INT UNSIGNED,
    process_id VARBINARY(255) DEFAULT NULL,
    CONSTRAINT uniq_username_email UNIQUE (username, email)
);

