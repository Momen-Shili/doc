USE doctor_saas;

INSERT INTO doctors (full_name, email, password_hash, specialty, phone) VALUES ('Dr Demo', 'demo@exemple.fr', '$2y$10$uXXGeyHMsNsBu2TOrbL0gu.yIp6MoPzNNNf.rwXXwGKKoXlreGqKK', 'Generaliste', '0102030405');

INSERT INTO admins (name, email, password_hash) VALUES
('Admin', 'admin@exemple.fr', '$2y$10$Ec1eOLxjhV5oNGOP1q8uc.VSkg.51cMF2hVka7J3HMHspwOlcmeHS'),
('Naro Team', 'admin@naroteam.com', '$2y$10$pcwMy7ODCCrkvAvDI3iPzes89HVVYWFY3AqNhc8dPNm3xBstR5VCG');

INSERT INTO platform_settings (setting_key, setting_value) VALUES ('app_name', 'Cabinet SaaS'),
       ('support_email', 'support@exemple.fr');
