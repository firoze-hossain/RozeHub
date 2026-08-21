-- RozeHub documentation versioning patch for phpMyAdmin/cPanel.
-- Run this AFTER the documentation tables have been created.
-- It makes documentation pages optionally belong to a specific software release.

SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE documentation_pages
    ADD COLUMN release_id BIGINT UNSIGNED NULL AFTER software_project_id;

ALTER TABLE documentation_pages
    ADD CONSTRAINT documentation_pages_release_id_foreign
    FOREIGN KEY (release_id) REFERENCES releases(id) ON DELETE SET NULL;

ALTER TABLE documentation_pages
    DROP INDEX documentation_pages_software_project_id_slug_unique;

ALTER TABLE documentation_pages
    ADD INDEX documentation_pages_project_release_slug_index
    (software_project_id, release_id, slug);

SET FOREIGN_KEY_CHECKS=1;
