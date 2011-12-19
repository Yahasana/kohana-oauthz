/**
 * OAuth database table schema for MySQL
 *
 * @author      sumh <42424861@qq.com>
 * @package     Oauth
 * @copyright   (c) 2011 OALite
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
	confirm_type TINYINT UNSIGNED DEFAULT 0
		COMMENT 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned' NOT NULL,
	client_level TINYINT UNSIGNED DEFAULT 0
		COMMENT 'diferent client levels have different max request times' NOT NULL,
	client_desc TEXT NULL,
	expires_in INTEGER UNSIGNED 
		COMMENT 'date time' NULL,
	scope VARCHAR(511) NULL,
	created INTEGER UNSIGNED NOT NULL,
	modified INTEGER UNSIGNED NULL,
	remark TEXT NULL
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
	server_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	client_id VARCHAR(127) 
		COMMENT 'AKA. API key' NOT NULL,
	client_secret VARCHAR(127) 
		COMMENT 'AKA. API secret' NOT NULL,
	redirect_uri VARCHAR(511) 
		COMMENT 'AKA. Callback URI' NOT NULL,
	scope VARCHAR(255) 
		COMMENT 'May be create, read, update or delete. so on so for' NULL,
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') DEFAULT 'plaintext'
		COMMENT 'Secret signature encrypt type. e.g' NOT NULL,
	ssh_key VARCHAR(511) 
		COMMENT 'SSH public keys' NULL,
	app_name VARCHAR(127) 
		COMMENT 'Application Name' NOT NULL,
	app_desc TEXT 
		COMMENT 'Application Description, When users authenticate via your app, this is what they\'ll see.' NULL,
	app_profile ENUM('webserver','native','useragent','autonomous') DEFAULT 'webserver'
		COMMENT 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients' NOT NULL,
	app_purpose VARCHAR(511) NULL,
	user_id BIGINT UNSIGNED 
		COMMENT 'Ref# from users table' NULL,
	user_level TINYINT UNSIGNED DEFAULT 0
		COMMENT 'diferent client levels have different max request times' NOT NULL,
	enabled TINYINT UNSIGNED DEFAULT 1
		COMMENT '0: waiting for system administrator audit; 1: acceptable; 2: ban' NOT NULL,
	created INTEGER UNSIGNED 
		COMMENT 'create datetime' NOT NULL,
	modified INTEGER UNSIGNED 
		COMMENT 'modified datetime' NULL
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
	log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
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
	token_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	client_id VARCHAR(127) NOT NULL,
	user_id BIGINT UNSIGNED 
		COMMENT 'Ref# from users table' NOT NULL,
	code VARCHAR(127) NOT NULL,
	access_token VARCHAR(63) NULL,
	refresh_token VARCHAR(63) NULL,
	expire_code INTEGER UNSIGNED DEFAULT 300
		COMMENT 'authorization code expires in this timestamp' NOT NULL,
	expire_token INTEGER UNSIGNED DEFAULT 0
		COMMENT 'access token expires in this timestamp' NOT NULL,
	expire_refresh INTEGER UNSIGNED DEFAULT 0
		COMMENT 'refresh token expires in this timestamp' NOT NULL,
	`timestamp` INTEGER UNSIGNED 
		COMMENT 'authorization code request timestamp' NOT NULL,
	token_type VARCHAR(127) 
		COMMENT 'bearer, mac, etc.' NOT NULL,
	options TEXT 
		COMMENT 'parameters for different token type extension in json format' NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */

/* Set Comments */
ALTER TABLE t_oauth_tokens COMMENT = 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_access_token ON t_oauth_tokens (access_token);
CREATE INDEX idx_t_oauth_tokens_client_id ON t_oauth_tokens (client_id);
CREATE INDEX idx_t_oauth_tokens_code ON t_oauth_tokens (code);
CREATE INDEX idx_t_oauth_tokens_refresh_token ON t_oauth_tokens (refresh_token);


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_clients ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_clients
	FOREIGN KEY (client_id) REFERENCES t_oauth_clients (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;