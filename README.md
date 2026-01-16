# Cabinet SaaS - Medecins independants

## Prerequis
- PHP 8.2+
- MySQL 8+

## Installation
1. Creer la base de donnees et les tables :
   - Importer `db/schema.sql`
   - (Optionnel) Importer `db/seed.sql`
   - (Optionnel) Importer `db/seed_demo.sql`

2. Configurer l'environnement :
   - Copier `.env.example.php` vers `.env.php`
   - Modifier les identifiants MySQL
   - Generer une cle APP_KEY :
     `php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"`
   - Renseigner `app_key` dans `.env.php`

3. Lancer le serveur PHP :
   - Dans le dossier racine :
     `php -S localhost:8000 -t public`

4. Acceder a l'application :
   - `http://localhost:8000`

## Compte de demonstration (seed_demo)
- Email : `demo@exemple.fr`
- Mot de passe : `demo1234`

## Acces admin (seed)
- URL : `http://localhost:8000/admin`
- Email : `admin@exemple.fr`
- Mot de passe : `admin1234`

## Avant deploiement : smoke tests
- Lancer : `php tools/smoke.php`
- Nettoyer les donnees de test : `php tools/smoke.php --clean`
- Le smoke test utilise un medecin dedie : `test@demo.tn` / `Demo123!`

## Notes
- Chaque medecin est un compte independant.
- Toutes les requetes filtrent par `doctor_id`.
- Paiements : especes uniquement.
- Chiffre d'affaires base uniquement sur `payments`.
- Devise : TND (3 decimales, millimes).
- Notifications reelles : file d'attente + worker CLI (pas d'envoi direct).

## Nouvelles tables
- `clinical_notes` (notes cliniques)
- `tags` + `patient_tags` (tags patients)
- `documents` (documents patients)
- `reminder_templates` (modeles de rappels)
- `reminders_log` (journal des rappels)
- `doctor_notification_settings` (parametres notifications)
- `notifications_outbox` (file d'envoi)
- `whatsapp_template_map` (mapping templates WhatsApp)
- `cash_closures` (clotures de caisse)
- `services` (catalogue des services)
- `appointment_services` (liaisons rendez-vous/services)
- `payment_items` (details des paiements)

## Migration manuelle (DB existante)
```
ALTER TABLE doctors
  ADD COLUMN theme_preference ENUM('dark','light') NOT NULL DEFAULT 'dark';

ALTER TABLE admins
  ADD COLUMN theme_preference ENUM('dark','light') NOT NULL DEFAULT 'dark';

ALTER TABLE patients
  ADD COLUMN consent_email TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN consent_sms TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN consent_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN whatsapp_phone VARCHAR(30) NULL;

ALTER TABLE payments
  ADD COLUMN receipt_number VARCHAR(30) NULL,
  ADD COLUMN created_by INT UNSIGNED NULL,
  ADD UNIQUE KEY uq_payments_doctor_receipt (doctor_id, receipt_number);

CREATE TABLE cash_closures (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  closure_date DATE NOT NULL,
  total_system DECIMAL(10,3) NOT NULL,
  cash_declared DECIMAL(10,3) NOT NULL,
  difference DECIMAL(10,3) NOT NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cash_closures_doctor_date (doctor_id, closure_date),
  CONSTRAINT fk_cash_closures_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  default_price DECIMAL(10,3) NOT NULL DEFAULT 0,
  default_duration_minutes INT NOT NULL DEFAULT 0,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_services_doctor_name (doctor_id, name),
  CONSTRAINT fk_services_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE appointment_services (
  appointment_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  PRIMARY KEY (appointment_id, service_id),
  CONSTRAINT fk_appointment_services_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  CONSTRAINT fk_appointment_services_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payment_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  payment_id INT UNSIGNED NOT NULL,
  service_id INT UNSIGNED NULL,
  description VARCHAR(255) NULL,
  qty INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,3) NOT NULL DEFAULT 0,
  line_total DECIMAL(10,3) NOT NULL DEFAULT 0,
  CONSTRAINT fk_payment_items_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_payment_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE reminder_templates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  channel ENUM('SMS','WHATSAPP','EMAIL','AUTRE') NOT NULL DEFAULT 'SMS',
  body TEXT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_reminder_templates_doctor_name (doctor_id, name),
  CONSTRAINT fk_reminder_templates_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reminders_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  appointment_id INT UNSIGNED NOT NULL,
  patient_id INT UNSIGNED NOT NULL,
  template_id INT UNSIGNED NULL,
  channel ENUM('SMS','WHATSAPP','EMAIL','AUTRE') NOT NULL DEFAULT 'SMS',
  message TEXT NOT NULL,
  status ENUM('DRAFT','SENT','FAILED') NOT NULL DEFAULT 'SENT',
  sent_at DATETIME NOT NULL,
  error_text TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_reminders_doctor_sent (doctor_id, sent_at),
  CONSTRAINT fk_reminders_log_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  CONSTRAINT fk_reminders_log_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
  CONSTRAINT fk_reminders_log_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_reminders_log_template FOREIGN KEY (template_id) REFERENCES reminder_templates(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE doctor_notification_settings (
  doctor_id BIGINT UNSIGNED PRIMARY KEY,
  smtp_host VARCHAR(190) NULL,
  smtp_port INT NULL,
  smtp_secure ENUM('tls','ssl','none') NOT NULL DEFAULT 'tls',
  smtp_username VARCHAR(190) NULL,
  smtp_password_enc TEXT NULL,
  smtp_from_email VARCHAR(190) NULL,
  smtp_from_name VARCHAR(190) NULL,
  infobip_base_url VARCHAR(255) NOT NULL DEFAULT 'https://6zjpx5.api.infobip.com',
  infobip_api_key_enc TEXT NULL,
  infobip_sms_sender VARCHAR(20) NULL,
  infobip_whatsapp_sender VARCHAR(50) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE notifications_outbox (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  doctor_id BIGINT UNSIGNED NOT NULL,
  reminder_log_id BIGINT UNSIGNED NOT NULL,
  channel ENUM('EMAIL','SMS','WHATSAPP') NOT NULL,
  to_address VARCHAR(255) NOT NULL,
  payload_json JSON NOT NULL,
  status ENUM('PENDING','SENT','FAILED') NOT NULL DEFAULT 'PENDING',
  tries INT NOT NULL DEFAULT 0,
  last_error TEXT NULL,
  next_retry_at DATETIME NULL,
  external_id VARCHAR(120) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_outbox_status_next (status, next_retry_at)
) ENGINE=InnoDB;

CREATE TABLE whatsapp_template_map (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  doctor_id BIGINT UNSIGNED NOT NULL,
  reminder_template_id BIGINT UNSIGNED NOT NULL,
  template_name VARCHAR(120) NOT NULL,
  language_code VARCHAR(10) NOT NULL DEFAULT 'fr',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wa_map (doctor_id, reminder_template_id)
) ENGINE=InnoDB;
```

- Upload CSV securise dans `storage/uploads/doctor_{id}/`.

## Import CSV
- Fichiers attendus : `patients.csv`, `appointments.csv`, `payments.csv`
- Etapes : upload -> mapping -> previsualisation -> validation -> import
- Support `external_id` pour l'idempotence

## MoM - regles de calcul
- Periode selectionnee : date_debut -> date_fin.
- Comparaison "periode precedente" : meme duree immediatement precedente.
- Comparaison "memes jours du mois precedent" : memes jours, avec troncature si necessaire.

## Logs
- Erreurs applicatives : `storage/logs/app.log`

## Notifications (Email/SMS/WhatsApp)
- Reglages: `?r=notifications-settings`
- SMS/WhatsApp: Infobip (`https://6zjpx5.api.infobip.com`)
- Email: SMTP personnel du medecin
- WhatsApp: associer un template Infobip dans les modeles de rappels
- Envoi via worker:
  `php tools/worker_send_notifications.php`
- Consentements:
  - Email: patient.email + consent_email = 1
  - SMS: patient.phone + consent_sms = 1
  - WhatsApp: patient.whatsapp_phone (ou phone) + consent_whatsapp = 1

## Checklist tests (manuel)
- Creer un service, verifier l'affichage dans la liste.
- Creer un rendez-vous avec services et valider le total et la duree suggeres.
- Enregistrer un paiement avec "Detailler le paiement" et verifier les lignes.
- Verifier les recettes par service et l'export CSV.

## UI audit (kit sombre)
- app/views/admin/layout.php
- app/views/admin/partials/sidebar.php
- app/views/admin/partials/topbar.php
- app/views/admin/pages/dashboard.php
- app/views/admin/pages/doctors.php
- app/views/admin/pages/payments.php
- app/views/admin/pages/settings.php
- app/views/admin/pages/login.php
- app/views/partials/flash.php
- app/views/pages/auth/login.php
- app/views/pages/auth/register.php
- app/views/pages/auth/profile.php
- app/views/pages/patients/index.php
- app/views/pages/patients/create.php
- app/views/pages/patients/edit.php
- app/views/pages/appointments/index.php
- app/views/pages/appointments/create.php
- app/views/pages/appointments/edit.php
- app/views/pages/payments/index.php
- app/views/pages/payments/create.php
- app/views/pages/import/index.php
- app/views/pages/import/map.php
- app/views/pages/import/preview.php
- app/views/pages/import/validate.php
- app/views/pages/import/result.php
- app/views/pages/services/index.php
- app/views/pages/services/create.php
- app/views/pages/services/edit.php
- app/views/pages/cash/index.php
- app/views/pages/payments/receipt.php
- app/views/pages/cash/closure_print.php
- app/views/pages/error.php

## Recette manuelle UI (10 min)
- Connexion
- Dashboard
- Patients -> fiche patient -> notes/documents/tags
- Services -> creer service
- RDV -> creer RDV avec service
- Paiement -> paiement detaille + recu
- Rapports -> recettes par service + export
- Caisse -> cloture + impression

## Checklist accessibilite (5 min)
- Navigation clavier : Tab/Shift+Tab depuis sidebar -> topbar -> contenu
- Focus visible : boutons, liens, champs, onglets, drawer et modals
- Modals/drawers : focus initial, piege de tabulation, ESC pour fermer, focus retour
- Formulaires : labels associes, erreurs annoncees (role="alert" / aria-describedby)
- Tableaux : entetes <th scope="col">, actions lisibles, etats vides clairs

## Checklist notifications (5 min)
- Configurer SMTP -> envoyer un test -> lancer le worker -> statut SENT
- Configurer Infobip -> envoyer SMS test -> worker -> external_id present
- Associer template WhatsApp -> test WhatsApp -> worker -> statut SENT
