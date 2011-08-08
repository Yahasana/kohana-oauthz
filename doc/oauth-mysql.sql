/**
 * OAuth database table schema for MySQL
 *
 * @author      sumh <42424861@qq.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 */

/******************** Add Table: t_oauth_authorizes ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_authorizes
(
	user_id BIGINT UNSIGNED NULL,
	client_id VARCHAR(127) NOT NULL,
	redirect_uri VARCHAR(511) NOT NULL,
	confirm_type TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned',
	client_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times',
	modified INTEGER UNSIGNED NULL,
	created INTEGER UNSIGNED NOT NULL,
	scope VARCHAR(511) NULL,
	expired_date INTEGER UNSIGNED NULL 
		COMMENT 'date time',
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_authorizes */
ALTER TABLE t_oauth_authorizes ADD CONSTRAINT pkt_oauth_authorizes
	PRIMARY KEY (user_id);

/* Set Comments */
ALTER TABLE t_oauth_authorizes COMMENT = 'Store audit information from resource owner for the resource requester';

/******************** Add Table: t_oauth_clients ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_clients
(
	server_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(127) NOT NULL 
		COMMENT 'AKA. API key',
	client_secret VARCHAR(127) NOT NULL 
		COMMENT 'AKA. API secret',
	redirect_uri VARCHAR(511) NOT NULL 
		COMMENT 'AKA. Callback URI',
	scope VARCHAR(255) NULL 
		COMMENT 'May be create, read, update or delete. so on so for',
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') NOT NULL DEFAULT 'plaintext'
		COMMENT 'Secret signature encrypt type. e.g',
	ssh_key VARCHAR(511) NULL 
		COMMENT 'SSH public keys',
	app_name VARCHAR(127) NOT NULL 
		COMMENT 'Application Name',
	app_desc TEXT NULL 
		COMMENT 'Application Description, When users authenticate via your app, this is what they\'ll see.',
	app_profile ENUM('webserver','native','useragent','autonomous') NOT NULL DEFAULT 'webserver'
		COMMENT 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients',
	app_purpose VARCHAR(511) NULL,
	user_id BIGINT UNSIGNED NULL 
		COMMENT 'Ref# from users table',
	user_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times',
	enabled TINYINT UNSIGNED NOT NULL DEFAULT 1
		COMMENT '0: waiting for system administrator audit; 1: acceptable; 2: ban',
	created INTEGER UNSIGNED NOT NULL 
		COMMENT 'create datetime',
	modified INTEGER UNSIGNED NULL 
		COMMENT 'modified datetime'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_clients */

/* Set Comments */
ALTER TABLE t_oauth_clients COMMENT = 'Used for verification of incoming requests. ';

/* Add Indexes for: t_oauth_clients */
CREATE UNIQUE INDEX idx_t_oauth_clients_client_id ON t_oauth_clients (client_id);

/******************** Add Table: t_oauth_logs ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_logs
(
	log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(127) NULL,
	token VARCHAR(63) NULL,
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

/******************** Add Table: t_oauth_tokens ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_tokens
(
	token_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(127) NOT NULL,
	code VARCHAR(127) NOT NULL,
	access_token VARCHAR(63) NOT NULL,
	refresh_token VARCHAR(63) NULL,
	expires_in INTEGER UNSIGNED NOT NULL DEFAULT 300,
	`timestamp` INTEGER UNSIGNED NOT NULL,
	token_type VARCHAR(31) NOT NULL 
		COMMENT 'bearer',
	user_id BIGINT NOT NULL 
		COMMENT 'Ref# from users table',
	option TEXT NULL 
		COMMENT 'parameters for different token type extension in json format'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */

/* Set Comments */
ALTER TABLE t_oauth_tokens COMMENT = 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_client_id ON t_oauth_tokens (client_id);


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_clients ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_clients
	FOREIGN KEY (client_id) REFERENCES t_oauth_clients (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;