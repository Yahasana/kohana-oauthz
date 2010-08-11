/**
 * OAuth database table schema
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 * *
 */

/******************** Add Table: t_oauth_clients ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_clients
(
	user_id BIGINT UNSIGNED NULL,
	client_id VARCHAR(128) NOT NULL,
	redirect_uri VARCHAR(512) NOT NULL,
	confirm_type TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned',
	client_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times',
	update_time TIMESTAMP NOT NULL,
	insert_time TIMESTAMP NOT NULL,
	scope VARCHAR(512) NULL,
	expired_date INTEGER UNSIGNED NULL
		COMMENT 'date time',
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_clients */
ALTER TABLE t_oauth_clients ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (user_id, client_id);

/******************** Add Table: t_oauth_audits ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_audits
(
	access_token VARCHAR(128) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	nonce VARCHAR(64) NULL,
	secret_type VARCHAR(32) NULL
) DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_audits */

/* Set Comments */
ALTER TABLE t_oauth_audits COMMENT = 'Audit the access token';

/******************** Add Table: t_oauth_logs ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_logs
(
	log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(128) NULL,
	token VARCHAR(64) NULL,
	user_id BIGINT UNSIGNED NULL,
	received TEXT NOT NULL,
	sent TEXT NOT NULL,
	body TEXT NOT NULL,
	notes TEXT NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	remote_ip BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_logs */

/* Set Comments */
ALTER TABLE t_oauth_logs COMMENT = 'Log table to hold all OAuth request when you enabled logging ';

/* Add Indexes for: t_oauth_logs */
CREATE INDEX idx_t_oauth_logs_client_id_log_id ON t_oauth_logs (client_id, log_id);

/******************** Add Table: t_oauth_servers ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_servers
(
	server_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNSIGNED NULL,
	client_id VARCHAR(128) NOT NULL,
	enabled TINYINT UNSIGNED NOT NULL DEFAULT 1,
	redirect_uri VARCHAR(512) NOT NULL,
	issue_date INTEGER UNSIGNED NULL
		COMMENT 'date time',
	`timestamp` TIMESTAMP NOT NULL,
	client_secret VARCHAR(128) NOT NULL,
	secret_type VARCHAR(64) NOT NULL,
	scope VARCHAR(512) NULL,
	public_cert VARCHAR(512) NULL,
	client_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_servers */

/* Set Comments */
ALTER TABLE t_oauth_servers COMMENT = 'Used for verification of incoming requests. ';

/* Add Indexes for: t_oauth_servers */
CREATE UNIQUE INDEX idx_t_oauth_servers_client_id ON t_oauth_servers (client_id);

/******************** Add Table: t_oauth_tokens ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_tokens
(
	token_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(128) NOT NULL,
	code VARCHAR(128) NOT NULL,
	user_id BIGINT NOT NULL,
	access_token VARCHAR(64) NOT NULL,
	`timestamp` INTEGER UNSIGNED NOT NULL,
	expire_in INTEGER UNSIGNED NOT NULL DEFAULT 300,
	refresh_token VARCHAR(64) NULL,
	nonce VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */

/* Set Comments */
ALTER TABLE t_oauth_tokens COMMENT = 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_server_id ON t_oauth_tokens (client_id);


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_servers ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_servers
	FOREIGN KEY (client_id) REFERENCES t_oauth_servers (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;
