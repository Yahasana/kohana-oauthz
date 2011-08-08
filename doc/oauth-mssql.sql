/**
 * OAuth database table schema for MSSQL
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
	user_id BIGINT NULL,
	client_id VARCHAR(127) NOT NULL,
	redirect_uri VARCHAR(511) NOT NULL,
	confirm_type TINYINT NOT NULL DEFAULT 0,
	client_level TINYINT NOT NULL DEFAULT 0,
	modified INTEGER NULL,
	created INTEGER NOT NULL,
	scope VARCHAR(511) NULL,
	expired_date INTEGER NULL,
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_authorizes */
ALTER TABLE t_oauth_authorizes ADD CONSTRAINT pkt_oauth_authorizes
	PRIMARY KEY (user_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned',
	'table', 't_oauth_authorizes', 'column', 'confirm_type';
EXEC sp_addextendedproperty 'MS_Description', 'diferent client levels have different max request times',
	'table', 't_oauth_authorizes', 'column', 'client_level';
EXEC sp_addextendedproperty 'MS_Description', 'date time',
	'table', 't_oauth_authorizes', 'column', 'expired_date';
EXEC sp_addextendedproperty 'MS_Description', 'Store audit information from resource owner for the resource requester',
	'table', t_oauth_authorizes, null, null;

/******************** Add Table: t_oauth_clients ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_clients
(
	server_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(127) NOT NULL,
	client_secret VARCHAR(127) NOT NULL,
	redirect_uri VARCHAR(511) NOT NULL,
	scope VARCHAR(255) NULL,
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') NOT NULL DEFAULT 'plaintext',
	ssh_key VARCHAR(511) NULL,
	app_name VARCHAR(127) NOT NULL,
	app_desc TEXT NULL,
	app_profile ENUM('webserver','native','useragent','autonomous') NOT NULL DEFAULT 'webserver',
	app_purpose VARCHAR(511) NULL,
	user_id BIGINT NULL,
	user_level TINYINT NOT NULL DEFAULT 0,
	enabled TINYINT NOT NULL DEFAULT 1,
	created INTEGER NOT NULL,
	modified INTEGER NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_clients */
ALTER TABLE t_oauth_clients ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (server_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'AKA. API key',
	'table', 't_oauth_clients', 'column', 'client_id';
EXEC sp_addextendedproperty 'MS_Description', 'AKA. API secret',
	'table', 't_oauth_clients', 'column', 'client_secret';
EXEC sp_addextendedproperty 'MS_Description', 'AKA. Callback URI',
	'table', 't_oauth_clients', 'column', 'redirect_uri';
EXEC sp_addextendedproperty 'MS_Description', 'May be create, read, update or delete. so on so for',
	'table', 't_oauth_clients', 'column', 'scope';
EXEC sp_addextendedproperty 'MS_Description', 'Secret signature encrypt type. e.g',
	'table', 't_oauth_clients', 'column', 'secret_type';
EXEC sp_addextendedproperty 'MS_Description', 'SSH public keys',
	'table', 't_oauth_clients', 'column', 'ssh_key';
EXEC sp_addextendedproperty 'MS_Description', 'Application Name',
	'table', 't_oauth_clients', 'column', 'app_name';
EXEC sp_addextendedproperty 'MS_Description', 'Application Description, When users authenticate via your app, this is what they''ll see.',
	'table', 't_oauth_clients', 'column', 'app_desc';
EXEC sp_addextendedproperty 'MS_Description', 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients',
	'table', 't_oauth_clients', 'column', 'app_profile';
EXEC sp_addextendedproperty 'MS_Description', 'Ref# from users table',
	'table', 't_oauth_clients', 'column', 'user_id';
EXEC sp_addextendedproperty 'MS_Description', 'diferent client levels have different max request times',
	'table', 't_oauth_clients', 'column', 'user_level';
EXEC sp_addextendedproperty 'MS_Description', '0: waiting for system administrator audit; 1: acceptable; 2: ban',
	'table', 't_oauth_clients', 'column', 'enabled';
EXEC sp_addextendedproperty 'MS_Description', 'create datetime',
	'table', 't_oauth_clients', 'column', 'created';
EXEC sp_addextendedproperty 'MS_Description', 'modified datetime',
	'table', 't_oauth_clients', 'column', 'modified';
EXEC sp_addextendedproperty 'MS_Description', 'Used for verification of incoming requests. ',
	'table', t_oauth_clients, null, null;

/* Add Indexes for: t_oauth_clients */
CREATE UNIQUE NONCLUSTERED INDEX idx_t_oauth_clients_client_id ON t_oauth_clients (client_id);

/******************** Add Table: t_oauth_logs ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_logs
(
	log_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(127) NULL,
	token VARCHAR(63) NULL,
	user_id BIGINT NULL,
	received TEXT NOT NULL,
	sent TEXT NOT NULL,
	body TEXT NOT NULL,
	notes TEXT NOT NULL,
	[timestamp] TIMESTAMP NOT NULL,
	remote_ip BIGINT NOT NULL
) DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_logs */
ALTER TABLE t_oauth_logs ADD CONSTRAINT pkt_oauth_logs
	PRIMARY KEY (log_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'Log table to hold all OAuth request when you enabled logging ',
	'table', t_oauth_logs, null, null;

/* Add Indexes for: t_oauth_logs */
CREATE NONCLUSTERED INDEX idx_t_oauth_logs_client_id_log_id ON t_oauth_logs (client_id, log_id);

/******************** Add Table: t_oauth_tokens ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_tokens
(
	token_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(127) NOT NULL,
	code VARCHAR(127) NOT NULL,
	access_token VARCHAR(63) NOT NULL,
	refresh_token VARCHAR(63) NULL,
	expires_in INTEGER NOT NULL DEFAULT 300,
	[timestamp] INTEGER NOT NULL,
	token_type VARCHAR(31) NOT NULL,
	user_id BIGINT NOT NULL,
	option TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */
ALTER TABLE t_oauth_tokens ADD CONSTRAINT pkt_oauth_tokens
	PRIMARY KEY (token_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'bearer',
	'table', 't_oauth_tokens', 'column', 'token_type';
EXEC sp_addextendedproperty 'MS_Description', 'Ref# from users table',
	'table', 't_oauth_tokens', 'column', 'user_id';
EXEC sp_addextendedproperty 'MS_Description', 'parameters for different token type extension in json format',
	'table', 't_oauth_tokens', 'column', 'option';
EXEC sp_addextendedproperty 'MS_Description', 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ',
	'table', t_oauth_tokens, null, null;

/* Add Indexes for: t_oauth_tokens */
CREATE NONCLUSTERED INDEX idx_t_oauth_tokens_client_id ON t_oauth_tokens (client_id);


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_clients ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_clients
	FOREIGN KEY (client_id) REFERENCES t_oauth_clients (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;