/**
 * OAuth database table schema for PostgreSql
 *
 * @author      sumh <oalite@gmail.com>
 * @package     Oauth
 * @copyright   (c) 2010 OALite
 * @license     ISC License (ISCL)
 * @link        http://www.oalite.cn
 */

/******************** Add Table: "t_oauth_clients" ************************/

/* Build Table Structure */
CREATE TABLE "t_oauth_clients"
(
	user_id BIGINT NULL,
	client_id VARCHAR(128) NOT NULL,
	redirect_uri VARCHAR(512) NOT NULL,
	confirm_type SMALLINT NOT NULL DEFAULT 0,
	client_level SMALLINT NOT NULL DEFAULT 0,
	modified INTEGER NULL,
	created INTEGER NOT NULL,
	scope VARCHAR(512) NULL,
	expired_date INTEGER NULL,
	remark TEXT NULL,
	client_desc TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_clients" */
ALTER TABLE "t_oauth_clients" ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (user_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_clients".confirm_type IS 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned';
COMMENT ON COLUMN "t_oauth_clients".client_level IS 'diferent client levels have different max request times';
COMMENT ON COLUMN "t_oauth_clients".expired_date IS 'date time';
COMMENT ON TABLE "t_oauth_clients" IS 'Store audit information from resource owner for the resource requester';

/******************** Add Table: "t_oauth_audits" ************************/

/* Build Table Structure */
CREATE TABLE "t_oauth_audits"
(
	access_token VARCHAR(128) NOT NULL,
	created INTEGER NOT NULL,
	nonce VARCHAR(64) NULL,
	secret_type VARCHAR(32) NULL
) DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_audits" */

/* Set Comments */
COMMENT ON TABLE "t_oauth_audits" IS 'Audit the access token';

/******************** Add Table: "t_oauth_logs" ************************/
 CREATE SEQUENCE seq_t_oauth_logs_log_id INCREMENT 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_logs"
(
	log_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_logs_log_id'),
	client_id VARCHAR(128) NULL,
	token VARCHAR(64) NULL,
	user_id BIGINT NULL,
	received TEXT NOT NULL,
	sent TEXT NOT NULL,
	body TEXT NOT NULL,
	notes TEXT NOT NULL,
	"timestamp" TIMESTAMP NOT NULL,
	remote_ip BIGINT NOT NULL
) DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_logs" */
ALTER TABLE "t_oauth_logs" ADD CONSTRAINT pkt_oauth_logs
	PRIMARY KEY (log_id);

/* Set Comments */
COMMENT ON TABLE "t_oauth_logs" IS 'Log table to hold all OAuth request when you enabled logging ';

/* Add Indexes for: t_oauth_logs */
CREATE INDEX idx_t_oauth_logs_client_id_log_id ON "t_oauth_logs" (client_id, log_id);

/******************** Add Table: "t_oauth_servers" ************************/
 CREATE SEQUENCE seq_t_oauth_servers_server_id INCREMENT 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_servers"
(
	server_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_servers_server_id'),
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
	user_level SMALLINT NOT NULL DEFAULT 0,
	enabled SMALLINT NOT NULL DEFAULT 0,
	created INTEGER NOT NULL,
	modified INTEGER NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_servers" */
ALTER TABLE "t_oauth_servers" ADD CONSTRAINT pkt_oauth_servers
	PRIMARY KEY (server_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_servers".client_id IS 'AKA. API key';
COMMENT ON COLUMN "t_oauth_servers".client_secret IS 'AKA. API secret';
COMMENT ON COLUMN "t_oauth_servers".redirect_uri IS 'AKA. Callback URI';
COMMENT ON COLUMN "t_oauth_servers".scope IS 'May be create, read, update or delete. so on so for';
COMMENT ON COLUMN "t_oauth_servers".secret_type IS 'Secret signature encrypt type. e.g';
COMMENT ON COLUMN "t_oauth_servers".ssh_key IS 'SSH public keys';
COMMENT ON COLUMN "t_oauth_servers".app_name IS 'Application Name';
COMMENT ON COLUMN "t_oauth_servers".app_desc IS 'Application Description, When users authenticate via your app, this is what they''ll see.';
COMMENT ON COLUMN "t_oauth_servers".app_profile IS 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients';
COMMENT ON COLUMN "t_oauth_servers".user_id IS 'Ref# from users table';
COMMENT ON COLUMN "t_oauth_servers".user_level IS 'diferent client levels have different max request times';
COMMENT ON COLUMN "t_oauth_servers".enabled IS '0: waiting for system administrator audit; 1: acceptable; 2: ban';
COMMENT ON COLUMN "t_oauth_servers".created IS 'create datetime';
COMMENT ON COLUMN "t_oauth_servers".modified IS 'modified datetime';
COMMENT ON TABLE "t_oauth_servers" IS 'Used for verification of incoming requests. ';

/* Add Indexes for: t_oauth_servers */
CREATE UNIQUE INDEX idx_t_oauth_servers_client_id ON "t_oauth_servers" (client_id);

/******************** Add Table: "t_oauth_tokens" ************************/
 CREATE SEQUENCE seq_t_oauth_tokens_token_id INCREMENT 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_tokens"
(
	token_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_tokens_token_id'),
	client_id VARCHAR(128) NOT NULL,
	code VARCHAR(128) NOT NULL,
	access_token VARCHAR(64) NOT NULL,
	refresh_token VARCHAR(64) NULL,
	expire_in INTEGER NOT NULL DEFAULT 300,
	"timestamp" INTEGER NOT NULL,
	nonce VARCHAR(64) NULL,
	user_id BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_tokens" */
ALTER TABLE "t_oauth_tokens" ADD CONSTRAINT pkt_oauth_tokens
	PRIMARY KEY (token_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_tokens".user_id IS 'Ref# from users table';
COMMENT ON TABLE "t_oauth_tokens" IS 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_server_id ON "t_oauth_tokens" (client_id);

/* Remove Schemas */
DROP SCHEMA "schemaA" CASCADE;


/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_servers ***************/
ALTER TABLE "t_oauth_tokens" ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_servers
	FOREIGN KEY (client_id) REFERENCES "t_oauth_servers" (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;
