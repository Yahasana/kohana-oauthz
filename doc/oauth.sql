/******************** Add Table: t_oauth_authorizeds ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_authorizeds
(
	authorized_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(64) NOT NULL,
	auth_desc VARCHAR(512) NULL,
	status TINYINT UNSIGNED NOT NULL DEFAULT 1
		COMMENT '0: disable; 1: enable; 2: ask me every time',
	remark TEXT NULL 
		COMMENT 'memo or some note',
	insert_by VARCHAR(64) NULL,
	update_by VARCHAR(64) NULL,
	update_time TIMESTAMP NOT NULL,
	insert_time TIMESTAMP NOT NULL,
	scope VARCHAR(512) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/******************** Add Table: t_oauth_client_tokens ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_client_tokens
(
	token_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id BIGINT UNSIGNED NOT NULL,
	user_id BIGINT UNSIGNED NOT NULL,
	username VARCHAR(64) NOT NULL,
	token VARCHAR(64) NOT NULL,
	token_secret VARCHAR(64) NOT NULL,
	token_type ENUM('request','authorized','access') NULL 
		COMMENT 'request,authorized,access',
	token_ttl DATETIME NOT NULL DEFAULT '9999-12-31 00:00:00',
	`timestamp` TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_client_tokens */

/* Add Indexes for: t_oauth_client_tokens */
CREATE INDEX idx_t_oauth_client_tokens_client_id ON t_oauth_client_tokens (client_id);

/******************** Add Table: t_oauth_clients ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_clients
(
	id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNSIGNED NULL,
	client_id VARCHAR(64) NOT NULL,
	client_secret VARCHAR(64) NOT NULL,
	secrect_type VARCHAR(64) NOT NULL 
		COMMENT 'HMAC-SHA1,PLAINTEXT',
	server_uri VARCHAR(256) NOT NULL,
	server_uri_host VARCHAR(128) NOT NULL,
	server_uri_path VARCHAR(128) NOT NULL,
	request_token_uri VARCHAR(256) NOT NULL,
	authorize_uri VARCHAR(256) NOT NULL,
	access_token_uri VARCHAR(256) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	scope VARCHAR(512) NULL,
	client_level TINYINT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'diferent client levels have different max request times'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/******************** Add Table: t_oauth_logs ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_logs
(
	log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(64) NULL,
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

/******************** Add Table: t_oauth_nonces ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_nonces
(
	nonce_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	client_id VARCHAR(64) NOT NULL,
	token VARCHAR(64) NOT NULL,
	`timestamp` BIGINT NOT NULL,
	nonce VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/******************** Add Table: t_oauth_servers ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_servers
(
	server_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id BIGINT UNSIGNED NULL,
	client_id VARCHAR(64) NOT NULL,
	enabled TINYINT UNSIGNED NOT NULL DEFAULT 1,
	redirect_uri VARCHAR(256) NOT NULL,
	issue_date DATETIME NULL,
	`timestamp` TIMESTAMP NOT NULL,
	secrect_type VARCHAR(64) NOT NULL,
	client_secrect VARCHAR(128) NOT NULL,
	secret_type VARCHAR(64) NOT NULL,
	scope VARCHAR(512) NULL,
	private_cert VARCHAR(512) NULL
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
	server_id BIGINT UNSIGNED NOT NULL,
	code VARCHAR(128) NOT NULL,
	user_id INTEGER NOT NULL,
	access_token VARCHAR(64) NOT NULL,
	token_secret VARCHAR(64) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	expire_in INTEGER UNSIGNED NOT NULL DEFAULT 300,
	refresh_token VARCHAR(64) NULL,
	secret_type VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */

/* Set Comments */
ALTER TABLE t_oauth_tokens COMMENT = 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_server_id ON t_oauth_tokens (server_id);


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_oauth_consumer_tokens_oauth_consumer_registries ***************/
ALTER TABLE t_oauth_client_tokens ADD CONSTRAINT fk_oauth_consumer_tokens_oauth_consumer_registries
	FOREIGN KEY (client_id) REFERENCES t_oauth_clients (id)
	ON UPDATE CASCADE ON DELETE CASCADE;

/************ Foreign Key: fk_oauth_server_tokens_oauth_server_registries ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_oauth_server_tokens_oauth_server_registries
	FOREIGN KEY (server_id) REFERENCES t_oauth_servers (server_id)
	ON UPDATE CASCADE ON DELETE CASCADE;