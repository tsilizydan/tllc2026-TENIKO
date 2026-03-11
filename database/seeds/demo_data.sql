-- TENIKO Seed Data — Demo content for development
USE `teniko`;

-- Site Settings
INSERT INTO `site_settings` (`key`, `value`, `type`, `group`, `label`) VALUES
('site_name',        'TENIKO',                                          'text',    'general',  'Site Name'),
('site_tagline',     'The Living Archive of Malagasy Language & Culture', 'text',  'general',  'Tagline'),
('site_email',       'contact@teniko.mg',                               'text',    'general',  'Contact Email'),
('maintenance_mode', '0',                                               'boolean', 'general',  'Maintenance Mode'),
('word_of_day_manual','0',                                              'boolean', 'general',  'Manual Word of Day'),
('proverb_of_day_manual','0',                                          'boolean', 'general',  'Manual Proverb of Day'),
('allow_registration','1',                                             'boolean', 'users',    'Allow Registration'),
('require_email_verify','1',                                           'boolean', 'users',    'Require Email Verification'),
('google_analytics', '',                                               'text',    'analytics','Google Analytics ID'),
('footer_text',      '© 2026 TENIKO. Preserving Malagasy Language & Culture.', 'text', 'general', 'Footer Text'),
('primary_color',    '#2E7D32',                                        'color',   'appearance','Primary Color'),
('accent_color',     '#C8102E',                                        'color',   'appearance','Accent Color');

-- Admin User (password: Admin@123)
INSERT INTO `users` (`username`,`email`,`password`,`display_name`,`role`,`status`,`email_verified_at`,`created_at`) VALUES
('admin','admin@teniko.mg','$2y$12$YV8Cy5pJlNtrHe9MRaOxHe9e3MxJy8Y3lR7TQdZ1ZKWvLZ5dqoLJm','TENIKO Admin','admin','active',NOW(),NOW());

-- Dialects
INSERT INTO `dialects` (`name`,`code`,`region`,`description`,`lat`,`lng`) VALUES
('Merina',      'mer', 'Hauts Plateaux',      'The prestige dialect of Madagascar, spoken in the central highlands around Antananarivo.',   -18.9137, 47.5360),
('Betsimisaraka','bts', 'Côte Est',           'Dialect spoken along the eastern coast of Madagascar.',                                       -18.0000, 49.0500),
('Betsileo',    'bsl', 'Hauts Plateaux Sud',  'Dialect of the Betsileo people, spoken in the southern highlands.',                          -20.2500, 46.9000),
('Sakalava',    'skl', 'Côte Ouest',          'Dialect spoken along the western coast.',                                                     -16.0000, 45.5000),
('Tsimihety',   'tsi', 'Nord',                'Dialect of the northern interior region.',                                                    -15.0000, 48.5000),
('Antandroy',   'ant', 'Extrême Sud',         'Dialect of the Antandroy people of the far south.',                                          -24.5000, 45.5000),
('Vezo',        'vez', 'Côte Sud-Ouest',      'Dialect of the Vezo fishing communities along the southwest coast.',                          -22.5000, 43.7000),
('Antaisaka',   'ais', 'Sud-Est',             'Dialect of the southeast region.',                                                            -23.0000, 47.5000);

-- Sample Words
INSERT INTO `words` (`word`,`slug`,`pronunciation`,`part_of_speech`,`status`,`featured`,`created_at`) VALUES
('teny',   'teny',   'ˈtenɪ',  'noun',    'published', 1, NOW()),
('fitiavana','fitiavana','fiˌtiaˈvanə','noun','published',1, NOW()),
('lalana', 'lalana', 'laˈlana', 'noun',    'published', 1, NOW()),
('fahalalana','fahalalana','faˌhalaˈlana','noun','published',0, NOW()),
('mahafinaritra','mahafinaritra','maˌhafinariˈtra','adjective','published',0,NOW()),
('vary',   'vary',   'ˈvarɪ',  'noun',    'published', 0, NOW()),
('rano',   'rano',   'ˈranʊ',  'noun',    'published', 0, NOW()),
('trano',  'trano',  'ˈtranʊ', 'noun',    'published', 0, NOW()),
('any',    'any',    'ˈanɪ',   'adverb',  'published', 0, NOW()),
('manan-karena','manan-karena','','adjective','published',0, NOW());

-- Definitions
INSERT INTO `definitions` (`word_id`,`lang`,`text`,`example`) VALUES
(1,'mg','Teny, resaka, fitenenana','Maro ny teny malagasy.'),
(1,'fr','Mot, parole, langage','Il y a beaucoup de mots malgaches.'),
(1,'en','Word, language, speech','There are many Malagasy words.'),
(2,'mg','Fitiavana, ny fitsipiky ny fo','Lehibe ny fitiavana.'),
(2,'fr','Amour, affection','L\'amour est grand.'),
(2,'en','Love, affection, caring','Love is great.'),
(3,'mg','Arabe, lalam-pirenena, fomba fiainana','Lalana malalaka io.'),
(3,'fr','Route, chemin, rue','Cette route est large.'),
(3,'en','Road, path, street, way','This road is wide.'),
(6,'mg','Sakafo malagasy fototra, vary be venty','Vary no sakafo lehibe eto Madagasikara.'),
(6,'fr','Riz, l\'aliment de base malgache','Le riz est l\'aliment principal à Madagascar.'),
(6,'en','Rice, the staple food of Madagascar','Rice is the main food in Madagascar.');

-- Translations
INSERT INTO `translations` (`word_id`,`lang`,`translation`) VALUES
(1,'fr','mot, langage, parole'),
(1,'en','word, language, speech'),
(2,'fr','amour, affection'),
(2,'en','love, affection'),
(3,'fr','route, chemin'),
(3,'en','road, path, way'),
(6,'fr','riz'),
(6,'en','rice');

-- Proverbs
INSERT INTO `proverbs` (`text`,`slug`,`translation_fr`,`translation_en`,`meaning`,`cultural_explanation`,`status`,`created_at`) VALUES
('Ny fitiavana tsy mba lany.',
 'ny-fitiavana-tsy-mba-lany',
 'L\'amour ne s\'épuise jamais.',
 'Love never runs out.',
 'L\'amour est inépuisable et toujours renouvelé.',
 'Ce proverbe malgache exprime la profondeur et la permanence de l\'amour, une valeur centrale dans la culture malagasy.',
 'published', NOW()),
('Ny hazo no ahijin-damba, ny maty no ahijin-tsambatra.',
 'ny-hazo-no-ahijin-damba',
 'C\'est sur l\'arbre que le vêtement est étendu, c\'est sur le mort que repose le bonheur des survivants.',
 'The garment is hung on the tree, happiness rests on those who have passed.',
 'Les ancêtres veillent sur les vivants.',
 'Ce proverbe parle du lien sacré entre les vivants et les ancêtres, un concept fondamental du fomba gasy (tradition malagasy).',
 'published', NOW()),
('Aleo very tsikalakalam-bola toy izay very tsikalakalam-pihavanana.',
 'aleo-very-tsikalakalam-bola',
 'Il vaut mieux perdre un peu d\'argent que de perdre un peu d\'amitié.',
 'Better to lose a little money than to lose a little friendship.',
 'Les relations humaines valent plus que l\'argent.',
 'Ce proverbe illustre la primauté du pihavanana (liens familiaux et d\'amitié) sur les biens matériels.',
 'published', NOW()),
('Ny teny lava mampisy fo, ny teny fohy mampisy aina.',
 'ny-teny-lava-mampisy-fo',
 'Les longues paroles découragent, les courtes paroles vivifient.',
 'Long words discourage, short words bring life.',
 'La concision dans la parole est une vertu.',
 'Ce proverbe enseigne la valeur de la communication claire et brève.',
 'published', NOW()),
('Aza mihambo raha tsy mahibo.',
 'aza-mihambo-raha-tsy-mahibo',
 'Ne te vante pas si tu ne sais pas accomplir.',
 'Don\'t boast if you can\'t deliver.',
 'La modestie est une vertu, les actes parlent plus que les mots.',
 'Un proverbe qui enseigne l\'humilité et l\'authenticité.',
 'published', NOW());

-- Word of Day (set teny as today's)
UPDATE `words` SET `word_of_day_date` = CURDATE() WHERE `slug` = 'teny';

-- Proverb of Day
UPDATE `proverbs` SET `proverb_of_day_date` = CURDATE() WHERE `slug` = 'ny-fitiavana-tsy-mba-lany';

-- Categories
INSERT INTO `categories` (`name`,`slug`,`type`,`description`,`sort_order`) VALUES
('Traditions & Fomba',  'traditions',  'cultural', 'Malagasy customs, ceremonies, and traditional practices.',    1),
('Histoire & Patrimoine','histoire',    'article',  'Historical texts and heritage documentation.',                2),
('Littérature & Poésie','litterature', 'article',  'Malagasy literature, poetry, and oral traditions.',           3),
('Linguistique',        'linguistique','article',  'Linguistic research and analysis of Malagasy.',               4),
('Noms & Toponymie',    'noms',        'cultural', 'Malagasy personal names and place names explained.',          5),
('Folklore',            'folklore',    'cultural', 'Myths, legends, and folk stories.',                           6);

-- Forums
INSERT INTO `forums` (`name`,`slug`,`description`,`sort_order`) VALUES
('Fiteny Malagasy',  'fiteny-malagasy',  'Discussions sur la langue malagasy — grammaire, orthographe, vocabulaire.',              1),
('Dialekta',         'dialekta',         'Questions et échanges sur les dialectes régionaux.',                                      2),
('Fomba & Kolontsaina','fomba-kolontsaina','Traditions, culture et patrimoine malagasy.',                                           3),
('Ohabolana',        'ohabolana',        'Proverbes : interprétations, origines et significations.',                               4),
('Fampahalalana',    'fampahalalana',    'Aide pour apprentissage du malagasy — débutants bienvenus.',                              5),
('Fikarohana',       'fikarohana',       'Recherches linguistiques, projets académiques et collaborations.',                        6);

-- Badges
INSERT INTO `badges` (`name`,`slug`,`description`,`icon`,`color`) VALUES
('Language Guardian',   'language-guardian',  'Contributed 50+ words to the dictionary.',   'fa-shield-alt',    '#2E7D32'),
('Proverb Keeper',      'proverb-keeper',     'Added or curated 10+ proverbs.',              'fa-scroll',        '#C8102E'),
('Culture Contributor', 'culture-contributor','Published 5+ cultural articles.',             'fa-landmark',      '#7A4F2C'),
('Dialect Expert',      'dialect-expert',     'Contributed word variants in 3+ dialects.',   'fa-map-marked-alt','#2E7D32'),
('First Contribution',  'first-contribution', 'Made their first contribution to TENIKO.',    'fa-star',          '#F5E6C8'),
('Forum Elder',         'forum-elder',        'Posted 100+ forum replies.',                  'fa-comments',      '#2F2F2F'),
('Pronunciation Master','pronunciation-master','Uploaded 20+ audio pronunciations.',         'fa-microphone',    '#C8102E');

-- Announcements
INSERT INTO `announcements` (`title`,`body`,`type`,`is_active`,`created_by`) VALUES
('Bienvenue sur TENIKO !', 'TENIKO est maintenant en ligne. Aidez-nous à enrichir la plus grande encyclopédie numérique de la langue et culture malagasy. Contribuez, commentez, partagez !', 'info', 1, 1),
('Appel à contribution', 'Nous cherchons des contributeurs pour enrichir notre base de données de proverbes et de mots dialectaux. Rejoignez la communauté TENIKO !', 'success', 1, 1);
