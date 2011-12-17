/**
 * OAuth database table schema for PostgreSql
 *
 * @author      sumh <42424861@qq.com>
 * @package     Oauth
 * @copyright   (c) 2011 OALite
 * @license     ISC License (ISCL)
 * @link        http://oalite.com
 */

/******************** Add Table: "t_oauth_authorizes" ************************/

/* Build Table Structure */
CREATE TABLE "t_oauth_authorizes"
(
	user_id BIGINT NULL,
	client_id VARCHAR(127) NOT NULL,
	redirect_uri VARCHAR(511) NOT NULL,
	confirm_type SMALLINT DEFAULT 0 NOT NULL,
	client_level SMALLINT DEFAULT 0 NOT NULL,
	client_desc TEXT NULL,
	expires_in INTEGER NULL,
	scope VARCHAR(511) NULL,
	created INTEGER NOT NULL,
	modified INTEGER NULL,
	remark TEXT NULL
) DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_authorizes" */
ALTER TABLE "t_oauth_authorizes" ADD CONSTRAINT pkt_oauth_authorizes
	PRIMARY KEY (user_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_authorizes".confirm_type IS 'Request confirm, 0: every time; 1: only once; 2: with expired period; 3: once and banned';
COMMENT ON COLUMN "t_oauth_authorizes".client_level IS 'diferent client levels have different max request times';
COMMENT ON COLUMN "t_oauth_authorizes".expires_in IS 'date time';
COMMENT ON TABLE "t_oauth_authorizes" IS 'Store audit information from resource owner for the resource requester';

/******************** Add Table: "t_oauth_clients" ************************/
CREATE SEQUENCE seq_t_oauth_clients_server_id INCREMENT BY 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_clients"
(
	server_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_clients_server_id'),
	client_id VARCHAR(127) NOT NULL,
	client_secret VARCHAR(127) NOT NULL,
	redirect_uri VARCHAR(511) NOT NULL,
	scope VARCHAR(255) NULL,
	secret_type ENUM('plaintext','md5','rsa-sha1','hmac-sha1') DEFAULT 'plaintext' NOT NULL,
	ssh_key VARCHAR(511) NULL,
	app_name VARCHAR(127) NOT NULL,
	app_desc TEXT NULL,
	app_profile ENUM('webserver','native','useragent','autonomous') DEFAULT 'webserver' NOT NULL,
	app_purpose VARCHAR(511) NULL,
	user_id BIGINT NULL,
	user_level SMALLINT DEFAULT 0 NOT NULL,
	enabled SMALLINT DEFAULT 1 NOT NULL,
	created INTEGER NOT NULL,
	modified INTEGER NULL
) DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_clients" */
ALTER TABLE "t_oauth_clients" ADD CONSTRAINT pkt_oauth_clients
	PRIMARY KEY (server_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_clients".client_id IS 'AKA. API key';
COMMENT ON COLUMN "t_oauth_clients".client_secret IS 'AKA. API secret';
COMMENT ON COLUMN "t_oauth_clients".redirect_uri IS 'AKA. Callback URI';
COMMENT ON COLUMN "t_oauth_clients".scope IS 'May be create, read, update or delete. so on so for';
COMMENT ON COLUMN "t_oauth_clients".secret_type IS 'Secret signature encrypt type. e.g';
COMMENT ON COLUMN "t_oauth_clients".ssh_key IS 'SSH public keys';
COMMENT ON COLUMN "t_oauth_clients".app_name IS 'Application Name';
COMMENT ON COLUMN "t_oauth_clients".app_desc IS 'Application Description, When users authenticate via your app, this is what they''ll see.';
COMMENT ON COLUMN "t_oauth_clients".app_profile IS 'Application Profile: Web Server Application, Native Application, Browser Application, Autonomous clients';
COMMENT ON COLUMN "t_oauth_clients".user_id IS 'Ref# from users table';
COMMENT ON COLUMN "t_oauth_clients".user_level IS 'diferent client levels have different max request times';
COMMENT ON COLUMN "t_oauth_clients".enabled IS '0: waiting for system administrator audit; 1: acceptable; 2: ban';
COMMENT ON COLUMN "t_oauth_clients".created IS 'create datetime';
COMMENT ON COLUMN "t_oauth_clients".modified IS 'modified datetime';
COMMENT ON TABLE "t_oauth_clients" IS 'Used for verification of incoming requests. ';

/* Add Indexes for: t_oauth_clients */
CREATE UNIQUE INDEX idx_t_oauth_clients_client_id ON "t_oauth_clients" (client_id);

/******************** Add Table: "t_oauth_logs" ************************/
CREATE SEQUENCE seq_t_oauth_logs_log_id INCREMENT BY 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_logs"
(
	log_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_logs_log_id'),
	client_id VARCHAR(127) NULL,
	token VARCHAR(63) NULL,
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

/******************** Add Table: "t_oauth_tokens" ************************/
CREATE SEQUENCE seq_t_oauth_tokens_token_id INCREMENT BY 1;

/* Build Table Structure */
CREATE TABLE "t_oauth_tokens"
(
	token_id BIGINT NOT NULL DEFAULT nextval('seq_t_oauth_tokens_token_id'),
	client_id VARCHAR(127) NOT NULL,
	user_id BIGINT NOT NULL,
	code VARCHAR(127) NOT NULL,
	access_token VARCHAR(63) NOT NULL,
	refresh_token VARCHAR(63) NULL,
	expire_code INTEGER DEFAULT 300 NOT NULL,
	expire_token INTEGER DEFAULT 0 NOT NULL,
	expire_refresh INTEGER DEFAULT 0 NOT NULL,
	"timestamp" INTEGER NOT NULL,
	token_type VARCHAR(31) NOT NULL,
	option TEXT NULL
) DEFAULT CHARSET=utf8;

/* Table Items: "t_oauth_tokens" */
ALTER TABLE "t_oauth_tokens" ADD CONSTRAINT pkt_oauth_tokens
	PRIMARY KEY (token_id);

/* Set Comments */
COMMENT ON COLUMN "t_oauth_tokens".user_id IS 'Ref# from users table';
COMMENT ON COLUMN "t_oauth_tokens".expire_code IS 'authorization code expires in this timestamp';
COMMENT ON COLUMN "t_oauth_tokens".expire_token IS 'access token expires in this timestamp';
COMMENT ON COLUMN "t_oauth_tokens".expire_refresh IS 'refresh token expires in this timestamp';
COMMENT ON COLUMN "t_oauth_tokens"."timestamp" IS 'authorization code request timestamp';
COMMENT ON COLUMN "t_oauth_tokens".token_type IS 'bearer, mac, etc.';
COMMENT ON COLUMN "t_oauth_tokens".option IS 'parameters for different token type extension in json format';
COMMENT ON TABLE "t_oauth_tokens" IS 'Table used to verify signed requests sent to a server by the consumer.When the verification is succesful then the associated user id is returned. ';

/* Add Indexes for: t_oauth_tokens */
CREATE INDEX idx_t_oauth_tokens_client_id ON "t_oauth_tokens" (client_id);

/************ Add Foreign Keys to Database ***************/

/************ Foreign Key: fk_t_oauth_tokens_t_oauth_clients ***************/
ALTER TABLE "t_oauth_tokens" ADD CONSTRAINT fk_t_oauth_tokens_t_oauth_clients
	FOREIGN KEY (client_id) REFERENCES "t_oauth_clients" (client_id)
	ON UPDATE CASCADE ON DELETE CASCADE;