/**
 * OAuth database table schema for MySQL
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
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
	modified INTEGER UNSIGNED NULL,
	created INTEGER UNSIGNED NOT NULL,
	scope VARCHAR(512) NULL,
	expired_date INTEGER UNSIGNED NULL
		COMMENT 'date time',
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_clients */
ALTER TABLE t_oauth_clients ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (user_id);

/* Set Comments */
ALTER TABLE t_oauth_clients COMMENT = 'Store audit information from resource owner for the resource requester';

/******************** Add Table: t_oauth_audits ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_audits
(
	access_token VARCHAR(128) NOT NULL,
	created INTEGER UNSIGNED NOT NULL,
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
) DEFAULT CHARSET=utf8;

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
	client_id VARCHAR(128) NOT NULL
		COMMENT 'AKA. API key',
	client_secret VARCHAR(128) NOT NULL
		COMMENT 'AKA. API secret',
	redirect_uri VARCHAR(512) NOT NULL
		COMMENT 'AKA. Callback URI',
	scope VARCHAR(256) NULL
		COMMENT 'May be create, read, update or delete. so on so for',
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') NOT NULL DEFAULT 'plaintext'
		COMMENT 'Secret signature encrypt type. e.g',
	ssh_key VARCHAR(512) NULL
		COMMENT 'SSH public keys',
	app_name VARCHAR(128) NOT NULL
		COMMENT 'Application Name',
	app_desc TEXT NULL
		COMMENT 'Application Description, When users authenticate via your app, this is what they\'ll see.',
	app_profile ENUM('webserver','native','useragent','autonomous') NOT NULL DEFAULT 'webserver'
		COMMENT 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients',
	app_purpose VARCHAR(512) NULL,
	user_id BIGINT UNSIGNED NULL
		COMMENT 'Ref# from users table',
	user_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times',
	enabled TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT '0: waiting for system administrator audit; 1: acceptable; 2: ban',
	created INTEGER UNSIGNED NOT NULL
		COMMENT 'create datetime',
	modified INTEGER UNSIGNED NULL
		COMMENT 'modified datetime'
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
	access_token VARCHAR(64) NOT NULL,
	refresh_token VARCHAR(64) NULL,
	expires_in INTEGER UNSIGNED NOT NULL DEFAULT 300,
	`timestamp` INTEGER UNSIGNED NOT NULL,
	nonce VARCHAR(64) NULL,
	user_id BIGINT NOT NULL
		COMMENT 'Ref# from users table'
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