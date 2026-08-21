-- RozeHub Documentation starter content for phpMyAdmin/cPanel.
-- Import this AFTER cpanel_install.sql if you want the prebuilt documentation library.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE documentation_pages;
TRUNCATE TABLE documentation_sections;

-- Helper data: one compact, editable starter library for all seven products.
-- The full Laravel migration contains the deeper NOVAOS / Roze / StratosDB content.

INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Overview','overview','Start here: product goals, concepts, and core workflow.','◉',0,1,NOW(),NOW() FROM software_projects WHERE slug='novaos';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Getting Started','getting-started','Installation, first boot, updates, and daily workflow.','→',1,1,NOW(),NOW() FROM software_projects WHERE slug='novaos';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'System Reference','system-reference','Architecture, storage, networking, services, and diagnostics.','⌘',2,1,NOW(),NOW() FROM software_projects WHERE slug='novaos';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Development','development','Build, release channels, and contributor workflow.','⌁',3,1,NOW(),NOW() FROM software_projects WHERE slug='novaos';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Overview','overview','The Roze language, compiler, runtime, and developer model.','{}',0,1,NOW(),NOW() FROM software_projects WHERE slug='roze-language';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Language Guide','language-guide','Syntax, types, memory, and concurrency.','Aa',1,1,NOW(),NOW() FROM software_projects WHERE slug='roze-language';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Tooling & Standard Library','tooling-standard-library','Compiler, modules, packages, diagnostics, and libraries.','⌘',2,1,NOW(),NOW() FROM software_projects WHERE slug='roze-language';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Overview','overview','StratosDB architecture and database-engine concepts.','◉',0,1,NOW(),NOW() FROM software_projects WHERE slug='stratosdb';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'SQL Reference','sql-reference','SQL syntax, types, transactions, indexes, and query planning.','SQL',1,1,NOW(),NOW() FROM software_projects WHERE slug='stratosdb';
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Engine Operations','engine-operations','Storage, backup, recovery, CLI, configuration, and troubleshooting.','⌘',2,1,NOW(),NOW() FROM software_projects WHERE slug='stratosdb';

INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Getting Started','getting-started','Installation and first steps.','→',0,1,NOW(),NOW() FROM software_projects WHERE slug NOT IN ('novaos','roze-language','stratosdb');
INSERT INTO documentation_sections (software_project_id,title,slug,description,icon,sort_order,is_published,created_at,updated_at)
SELECT id,'Guides','guides','Core workflows and practical how-to articles.','⌁',1,1,NOW(),NOW() FROM software_projects WHERE slug NOT IN ('novaos','roze-language','stratosdb');

-- NOVAOS pages
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'NOVAOS at a glance','novaos-at-a-glance','overview','Current','Understand NOVAOS as an independent operating system and learn its release vocabulary.','# NOVAOS at a glance\n\nNOVAOS is an **independent operating system** in the RozeHub ecosystem. It is distributed as an operating-system image, not as a Windows, macOS, or Linux application.\n\n## Release identity\n\nEvery build has a major version, version, build number, codename, channel, architecture, and SHA-256 checksum.\n\n| Field | Meaning |\n| --- | --- |\n| Major version | Product family |\n| Version | Public release |\n| Build number | Internal build identity |\n| Channel | Stable, Beta, or Nightly |\n| Architecture | x64 or ARM64 |',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='overview' WHERE p.slug='novaos';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Installation guide','installation-guide','installation','Current','Verify the image, prepare installation media, install the base system, and complete first boot.','# Installation guide\n\nNOVAOS is installed from an operating-system image.\n\n## Before installation\n\n- Confirm x64 or ARM64.\n- Download the intended release.\n- Verify the SHA-256 checksum.\n- Back up important data.\n\n```bash\nsha256sum novaos-2026.2.1-x86_64.iso\n```\n\nCompare the result with the checksum published by RozeHub.',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='getting-started' WHERE p.slug='novaos';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Architecture overview','architecture-overview','architecture','Current','A layered model of boot, kernel, core services, user space, and the Roze developer layer.','# Architecture overview\n\nNOVAOS can be reasoned about as: firmware → bootloader → kernel → core services → user space → Roze developer layer.\n\nThe operating-system boundary should remain explicit so system updates do not require rebuilding unrelated applications.',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='system-reference' WHERE p.slug='novaos';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Release channels','release-channels','release','Current','Stable, Beta, and Nightly build policies.','# Release channels\n\n**Stable** is intended for normal use. **Beta** is for broader testing. **Nightly** represents active development and may contain breaking changes.\n\n| Channel | Purpose | Risk |\n| --- | --- | --- |\n| Stable | General users | Low |\n| Beta | Validation | Medium |\n| Nightly | Development | High |',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='development' WHERE p.slug='novaos';

-- Roze pages
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Roze language overview','roze-language-overview','overview','Current','Core goals, syntax philosophy, and the first program.','# Roze language overview\n\nRoze is a systems-oriented programming language focused on clarity, explicitness, and predictable performance.\n\n```roze\nfn main() {\n    print("Hello, Roze!")\n}\n```',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='overview' WHERE p.slug='roze-language';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Installation and toolchain','installation-and-toolchain','installation','Current','Install the compiler and build a first project.','# Installation and toolchain\n\nVerify the compiler with `roze --version`.\n\n```text\nroze init my-app\ncd my-app\nroze build\nroze run\n```\n\nPin compiler versions in CI for reproducible builds.',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='overview' WHERE p.slug='roze-language';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Syntax and declarations','syntax-and-declarations','guide','Current','Variables, functions, expressions, and control flow.','# Syntax and declarations\n\nRoze favors declarations that make types and ownership visible.\n\n```roze\nlet count: Int = 10\n\nfn add(a: Int, b: Int) -> Int {\n    return a + b\n}\n```',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='language-guide' WHERE p.slug='roze-language';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Types and memory','types-and-memory','reference','Current','Primitive types, ownership, references, and resource lifetime.','# Types and memory\n\nThe type system should communicate the shape and lifetime of data. Important concepts include values, references, ownership, and deterministic resource cleanup.',1,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='language-guide' WHERE p.slug='roze-language';

-- StratosDB pages
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'StratosDB architecture','stratosdb-architecture','architecture','Current','Understand StratosDB as a database engine rather than a client application.','# StratosDB architecture\n\nStratosDB is a **database engine**.\n\n```text\nclient → parser → planner → executor → transaction layer → storage engine\n```\n\nThe storage layer owns durable data while transactions provide consistency boundaries.',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='overview' WHERE p.slug='stratosdb';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'SQL quick start','sql-quick-start','guide','Current','Create a table, insert rows, and query data.','# SQL quick start\n\n```sql\nCREATE TABLE departments (\n  id INTEGER PRIMARY KEY,\n  name TEXT NOT NULL\n);\n\nINSERT INTO departments VALUES (1, ''Engineering'');\nSELECT * FROM departments;\n```',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='sql-reference' WHERE p.slug='stratosdb';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Transactions and isolation','transactions-and-isolation','reference','Current','Atomic changes, commit, rollback, and concurrency behavior.','# Transactions and isolation\n\nUse transactions to make related changes atomic.\n\n```sql\nBEGIN;\nUPDATE accounts SET balance = balance - 100 WHERE id = 1;\nUPDATE accounts SET balance = balance + 100 WHERE id = 2;\nCOMMIT;\n```',1,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='sql-reference' WHERE p.slug='stratosdb';
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Backup and recovery','backup-and-recovery','operations','Current','Protect data and test restoration regularly.','# Backup and recovery\n\nA backup is useful only if it can be restored. Keep backups separate from the primary data directory, test restoration regularly, and record the engine version with every backup.',0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='engine-operations' WHERE p.slug='stratosdb';

-- Generic starter pages for the other four products.
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,CONCAT(p.name,' overview'),CONCAT(p.slug,'-overview'),'overview','Current','Product overview and core workflow.',CONCAT('# ',p.name,' overview\n\n',p.description,'\n\nUse this documentation as the starting point for installation, daily workflows, troubleshooting, and release notes.'),0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='getting-started' WHERE p.slug IN ('dbnavigator','thundercall','lumina','trackline');
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Getting started','getting-started','guide','Current','First steps and recommended setup.',CONCAT('# Getting started\n\nInstall the latest published release, open the application, and follow the first-run configuration.\n\n## Recommended workflow\n\nStart with a small project, verify the configuration, and keep project settings under version control where appropriate.'),1,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='getting-started' WHERE p.slug IN ('dbnavigator','thundercall','lumina','trackline');
INSERT INTO documentation_pages (software_project_id,documentation_section_id,title,slug,kind,version,summary,content,sort_order,is_published,created_at,updated_at)
SELECT p.id,s.id,'Core guides','core-guides','guide','Current','Practical workflows and repeatable tasks.',CONCAT('# Core guides\n\nUse this page as the home for practical workflows.\n\n- Configure the application\n- Create a project workflow\n- Save reusable settings\n- Export or share results\n\nAdd product-specific procedures from the RozeHub Admin Documentation panel.'),0,1,NOW(),NOW() FROM software_projects p JOIN documentation_sections s ON s.software_project_id=p.id AND s.slug='guides' WHERE p.slug IN ('dbnavigator','thundercall','lumina','trackline');

SET FOREIGN_KEY_CHECKS=1;

-- Version-aware documentation:
-- After importing the documentation library, run database/documentation_versioning.sql
-- if the documentation_pages.release_id column has not been created by Laravel migrations.
