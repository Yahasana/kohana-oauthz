/**
 * OAuth database table schema for MSSQL
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
	user_id BIGINT NULL,
	client_id VARCHAR(128) NOT NULL,
	redirect_uri VARCHAR(512) NOT NULL,
	confirm_type TINYINT NOT NULL DEFAULT 0,
	client_level TINYINT NOT NULL DEFAULT 0,
	modified INTEGER NULL,
	created INTEGER NOT NULL,
	scope VARCHAR(512) NULL,
	expired_date INTEGER NULL,
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_clients */
ALTER TABLE t_oauth_clients ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (user_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned',
	'table', 't_oauth_clients', 'column', 'confirm_type';
EXEC sp_addextendedproperty 'MS_Description', 'diferent client levels have different max request times',
	'table', 't_oauth_clients', 'column', 'client_level';
EXEC sp_addextendedproperty 'MS_Description', 'date time',
	'table', 't_oauth_clients', 'column', 'expired_date';
EXEC sp_addextendedproperty 'MS_Description', 'Store audit information from resource owner for the resource requester',
	'table', t_oauth_clients, null, null;

/******************** Add Table: t_oauth_audits ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_audits
(
	access_token VARCHAR(128) NOT NULL,
	created INTEGER NOT NULL,
	nonce VARCHAR(64) NULL,
	secret_type VARCHAR(32) NULL
) DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_audits */

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'Audit the access token',
	'table', t_oauth_audits, null, null;

/******************** Add Table: t_oauth_logs ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_logs
(
	log_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(128) NULL,
	token VARCHAR(64) NULL,
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

/******************** Add Table: t_oauth_servers ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_servers
(
	server_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(128) NOT NULL,
	client_secret VARCHAR(128) NOT NULL,
	redirect_uri VARCHAR(512) NOT NULL,
	scope VARCHAR(256) NULL,
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') NOT NULL DEFAULT 'plaintext',
	ssh_key VARCHAR(512) NULL,
	app_name VARCHAR(128) NOT NULL,
	app_desc TEXT NULL,
	app_profile ENUM('webserver','native','useragent','autonomous') NOT NULL DEFAULT 'webserver',
	app_purpose VARCHAR(512) NULL,
	user_id BIGINT NULL,
	user_level TINYINT NOT NULL DEFAULT 0,
	enabled TINYINT NOT NULL DEFAULT 0,
	created INTEGER NOT NULL,
	modified INTEGER NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_servers */
ALTER TABLE t_oauth_servers ADD CONSTRAINT pkt_oauth_servers
	PRIMARY KEY (server_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'AKA. API key',
	'table', 't_oauth_servers', 'column', 'client_id';
EXEC sp_addextendedproperty 'MS_Description', 'AKA. API secret',
	'table', 't_oauth_servers', 'column', 'client_secret';
EXEC sp_addextendedproperty 'MS_Description', 'AKA. Callback URI',
	'table', 't_oauth_servers', 'column', 'redirect_uri';
EXEC sp_addextendedproperty 'MS_Description', 'May be create, read, update or delete. so on so for',
	'table', 't_oauth_servers', 'column', 'scope';
EXEC sp_addextendedproperty 'MS_Description', 'Secret signature encrypt type. e.g',
	'table', 't_oauth_servers', 'column', 'secret_type';
EXEC sp_addextendedproperty 'MS_Description', 'SSH public keys',
	'table', 't_oauth_servers', 'column', 'ssh_key';
EXEC sp_addextendedproperty 'MS_Description', 'Application Name',
	'table', 't_oauth_servers', 'column', 'app_name';
EXEC sp_addextendedproperty 'MS_Description', 'Application Description, When users authenticate via your app, this is what they''ll see.',
	'table', 't_oauth_servers', 'column', 'app_desc';
EXEC sp_addextendedproperty 'MS_Description', 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients',
	'table', 't_oauth_servers', 'column', 'app_profile';
EXEC sp_addextendedproperty 'MS_Description', 'Ref# from users table',
	'table', 't_oauth_servers', 'column', 'user_id';
EXEC sp_addextendedproperty 'MS_Description', 'diferent client levels have different max request times',
	'table', 't_oauth_servers', 'column', 'user_level';
EXEC sp_addextendedproperty 'MS_Description', '0: waiting for system administrator audit; 1: acceptable; 2: ban',
	'table', 't_oauth_servers', 'column', 'enabled';
EXEC sp_addextendedproperty 'MS_Description', 'create datetime',
	'table', 't_oauth_servers', 'column', 'created';
EXEC sp_addextendedproperty 'MS_Description', 'modified datetime',
	'table', 't_oauth_servers', 'column', 'modified';
EXEC sp_addextendedproperty 'MS_Description', 'Used for verification of incoming requests. ',
	'table', t_oauth_servers, null, null;

/* Add Indexes for: t_oauth_servers */
CREATE UNIQUE NONCLUSTERED INDEX idx_t_oauth_servers_client_id ON t_oauth_servers (client_id);

/******************** Add Table: t_oauth_tokens ************************/

/* Build Table Structure */
CREATE TABLE t_oauth_tokens
(
	token_id BIGINT NOT NULL IDENTITY (1, 1),
	client_id VARCHAR(128) NOT NULL,
	code VARCHAR(128) NOT NULL,
	access_token VARCHAR(64) NOT NULL,
	refresh_token VARCHAR(64) NULL,
	expire_in INTEGER NOT NULL DEFAULT 300,
	[timestamp] INTEGER NOT NULL,
	nonce VARCHAR(64) NULL,
	user_id BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: t_oauth_tokens */
ALTER TABLE t_oauth_tokens ADD CONSTRAINT pkt_oauth_tokens
	PRIMARY KEY (token_id);

/* Set Comments */
EXEC sp_addextendedproperty 'MS_Description', 'Ref# from users table',
	'table', 't_oauth_tokens', 'column', 'user_id';
EXEC sp_addextendedproperty 'MS_Description', 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ',
	'table', t_oauth_tokens, null, null;

/* Add Indexes for: t_oauth_tokens */
CREATE NONCLUSTERED INDEX idx_t_oauth_tokens_server_id ON t_oauth_tokens (client_id);

/* Remove Schemas */
DROP SCHEMA schemaA;


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_servers ***************/
ALTER TABLE t_oauth_tokens ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_servers
	FOREIGN KEY (client_id) REFERENCES t_oauth_servers (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;