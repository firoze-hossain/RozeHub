-- RozeHub cPanel / phpMyAdmin installation
-- Import this file into an empty MySQL/MariaDB database.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS releases;
DROP TABLE IF EXISTS software_projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(255) NOT NULL,
 email VARCHAR(255) NOT NULL UNIQUE,
 email_verified_at TIMESTAMP NULL DEFAULT NULL,
 password VARCHAR(255) NOT NULL,
 is_admin TINYINT(1) NOT NULL DEFAULT 0,
 remember_token VARCHAR(100) NULL DEFAULT NULL,
 created_at TIMESTAMP NULL DEFAULT NULL,
 updated_at TIMESTAMP NULL DEFAULT NULL,
 PRIMARY KEY (id),
 KEY users_is_admin_index (is_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE software_projects (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(255) NOT NULL UNIQUE,
 slug VARCHAR(255) NOT NULL UNIQUE,
 tagline VARCHAR(255) NOT NULL,
 description TEXT NOT NULL,
 category VARCHAR(255) NOT NULL,
 accent VARCHAR(24) NOT NULL DEFAULT 'mint',
 icon VARCHAR(8) NOT NULL DEFAULT 'R',
 website VARCHAR(255) NULL,
 created_at TIMESTAMP NULL DEFAULT NULL,
 updated_at TIMESTAMP NULL DEFAULT NULL,
 PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE releases (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 software_project_id BIGINT UNSIGNED NOT NULL,
 version VARCHAR(255) NOT NULL,
 major_version VARCHAR(80) NULL,
 codename VARCHAR(80) NULL,
 build_number VARCHAR(80) NULL,
 platform VARCHAR(255) NOT NULL,
 architecture VARCHAR(255) NOT NULL DEFAULT 'x64',
 channel VARCHAR(255) NOT NULL DEFAULT 'Stable',
 minimum_version VARCHAR(80) NULL,
 is_mandatory TINYINT(1) NOT NULL DEFAULT 0,
 file_path VARCHAR(255) NULL,
 file_name VARCHAR(255) NULL,
 file_size BIGINT UNSIGNED NULL,
 sha256 VARCHAR(64) NULL,
 notes TEXT NULL,
 is_published TINYINT(1) NOT NULL DEFAULT 0,
 published_at TIMESTAMP NULL DEFAULT NULL,
 downloads_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
 created_at TIMESTAMP NULL DEFAULT NULL,
 updated_at TIMESTAMP NULL DEFAULT NULL,
 PRIMARY KEY (id),
 UNIQUE KEY release_identity_unique (software_project_id,version,platform,architecture,channel),
 KEY release_update_lookup_idx (software_project_id,platform,architecture,channel,is_published),
 CONSTRAINT releases_software_project_id_foreign FOREIGN KEY (software_project_id) REFERENCES software_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 software_project_id BIGINT UNSIGNED NOT NULL,
 author_name VARCHAR(255) NOT NULL,
 rating INT NOT NULL,
 body TEXT NOT NULL,
 is_approved TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP NULL DEFAULT NULL,
 updated_at TIMESTAMP NULL DEFAULT NULL,
 PRIMARY KEY (id),
 CONSTRAINT reviews_software_project_id_foreign FOREIGN KEY (software_project_id) REFERENCES software_projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name,email,password,is_admin,created_at,updated_at) VALUES
('RozeHub Administrator','admin@example.com','$2y$12$qfUy69lZTiUGXJevzFXXeOAX22dOxDJYLAs6WrUkoOO9LoRRPiOuy',1,NOW(),NOW());

INSERT INTO software_projects (name,slug,tagline,description,category,accent,icon,website,created_at,updated_at) VALUES
('DBNavigator','dbnavigator','A focused database client for serious data work.','Browse schemas, write queries, and understand your data without leaving your flow.','Database client','mint','D',NULL,NOW(),NOW()),
('ThunderCall','thundercall','API testing with a calmer, faster workflow.','Build requests, inspect responses, and share repeatable collections with your team.','API client','coral','T',NULL,NOW(),NOW()),
('StratosDB','stratosdb','A database engine designed for clear intent.','An experimental engine for reliable local data systems and developer-first control.','Database engine','gold','S',NULL,NOW(),NOW()),
('Lumina','lumina','An IDE that keeps the code in focus.','A lightweight home for projects, terminals, debugging, and the Roze language.','Development environment','lilac','L',NULL,NOW(),NOW()),
('Roze','roze-language','A language for understandable systems software.','The compiler, package tooling, and language runtime for building in Roze.','Programming language','blue','R',NULL,NOW(),NOW()),
('Roze OS','roze-os','A personal operating system experiment.','A focused operating system environment built around the Roze developer ecosystem.','Operating system','mint','O',NULL,NOW(),NOW()),
('Trackline','trackline','Activity visibility without the noise.','A considered employee activity tracker for teams that need clear, respectful reporting.','Workplace operations','coral','E',NULL,NOW(),NOW());

INSERT INTO releases (software_project_id,version,platform,architecture,channel,file_path,file_name,file_size,notes,is_published,published_at,downloads_count,created_at,updated_at) VALUES
(1,'1.4.0','Windows','x64','Stable',NULL,NULL,NULL,'Query history, connection profiles, and a faster table explorer.',1,NOW(),1842,NOW(),NOW()),
(2,'0.9.2','Linux','x64','Stable',NULL,NULL,NULL,'Collections now support environment variables and importable request groups.',1,NOW(),963,NOW(),NOW()),
(3,'0.5.1','Linux','x64','Stable',NULL,NULL,NULL,'Improved storage recovery and added an inspect command.',1,NOW(),427,NOW(),NOW()),
(4,'1.1.0','macOS','x64','Stable',NULL,NULL,NULL,'New workspace search and a redesigned extension manager.',1,NOW(),2135,NOW(),NOW()),
(5,'0.8.0','Windows','x64','Stable',NULL,NULL,NULL,'Pattern matching and improved diagnostics are now available.',1,NOW(),713,NOW(),NOW()),
(6,'0.3.0','Linux','x64','Stable',NULL,NULL,NULL,'Hardware detection and the initial installer experience.',1,NOW(),289,NOW(),NOW()),
(7,'2.0.0','Windows','x64','Stable',NULL,NULL,NULL,'New activity summary exports and configurable capture schedules.',1,NOW(),1568,NOW(),NOW());

INSERT INTO reviews (software_project_id,author_name,rating,body,is_approved,created_at,updated_at) VALUES
(1,'Mina R.',5,'The schema view is exactly the kind of calm visual feedback I want while exploring an unfamiliar database.',1,NOW(),NOW()),
(1,'Dev K.',4,'Fast to open and pleasantly direct. I would like to see more import options in a future release.',1,NOW(),NOW());

SET FOREIGN_KEY_CHECKS=1;
