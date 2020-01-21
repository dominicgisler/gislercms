-- Ergänzt session-Tabelle mit User Agent

ALTER TABLE `cms__session`
ADD COLUMN `user_agent` TEXT NOT NULL DEFAULT '' AFTER `browser`;
