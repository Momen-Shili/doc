-- Schema for doctor SaaS
CREATE DATABASE IF NOT EXISTS doctor_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doctor_saas;

CREATE TABLE doctors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  plan VARCHAR(50) NOT NULL DEFAULT 'standard',
  specialty VARCHAR(150) NULL,
  phone VARCHAR(50) NULL,
  theme_preference ENUM('dark','light') NOT NULL DEFAULT 'dark',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  suspended_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  theme_preference ENUM('dark','light') NOT NULL DEFAULT 'dark',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  external_id VARCHAR(64) NULL,
  full_name VARCHAR(150) NOT NULL,
  phone VARCHAR(50) NULL,
  email VARCHAR(190) NULL,
  whatsapp_phone VARCHAR(30) NULL,
  consent_email TINYINT(1) NOT NULL DEFAULT 0,
  consent_sms TINYINT(1) NOT NULL DEFAULT 0,
  consent_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
  dob DATE NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_patients_doctor_external (doctor_id, external_id),
  CONSTRAINT fk_patients_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE appointments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  external_id VARCHAR(64) NULL,
  patient_id INT UNSIGNED NOT NULL,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  status ENUM('PLANIFIE','CONFIRME','TERMINE','ANNULE','NO_SHOW') NOT NULL DEFAULT 'PLANIFIE',
  reason VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_appointments_doctor_external (doctor_id, external_id),
  INDEX idx_appointments_doctor_starts (doctor_id, starts_at),
  CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  external_id VARCHAR(64) NULL,
  patient_id INT UNSIGNED NOT NULL,
  appointment_id INT UNSIGNED NULL,
  received_at DATE NOT NULL,
  amount DECIMAL(10,3) NOT NULL,
  receipt_number VARCHAR(30) NULL,
  created_by INT UNSIGNED NULL,
  reference VARCHAR(100) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payments_doctor_external (doctor_id, external_id),
  UNIQUE KEY uq_payments_doctor_receipt (doctor_id, receipt_number),
  INDEX idx_payments_doctor_received (doctor_id, received_at),
  CONSTRAINT fk_payments_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  CONSTRAINT fk_payments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_payments_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

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

CREATE TABLE clinical_notes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  patient_id INT UNSIGNED NOT NULL,
  note_text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notes_doctor_patient (doctor_id, patient_id),
  CONSTRAINT fk_notes_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  CONSTRAINT fk_notes_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tags (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  color VARCHAR(30) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tags_doctor_name (doctor_id, name),
  CONSTRAINT fk_tags_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE patient_tags (
  patient_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (patient_id, tag_id),
  CONSTRAINT fk_patient_tags_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  CONSTRAINT fk_patient_tags_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT UNSIGNED NOT NULL,
  patient_id INT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  size_bytes INT UNSIGNED NOT NULL,
  note TEXT NULL,
  uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_documents_doctor_patient (doctor_id, patient_id),
  CONSTRAINT fk_documents_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  CONSTRAINT fk_documents_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
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

CREATE TABLE platform_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE doctor_notification_settings (
  doctor_id INT UNSIGNED PRIMARY KEY,
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
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_doctor_notif_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notifications_outbox (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  doctor_id INT UNSIGNED NOT NULL,
  reminder_log_id INT UNSIGNED NOT NULL,
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
  KEY idx_outbox_status_next (status, next_retry_at),
  KEY idx_outbox_doctor (doctor_id),
  CONSTRAINT fk_outbox_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id),
  CONSTRAINT fk_outbox_reminder FOREIGN KEY (reminder_log_id) REFERENCES reminders_log(id)
) ENGINE=InnoDB;

CREATE TABLE whatsapp_template_map (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  doctor_id INT UNSIGNED NOT NULL,
  reminder_template_id INT UNSIGNED NOT NULL,
  template_name VARCHAR(120) NOT NULL,
  language_code VARCHAR(10) NOT NULL DEFAULT 'fr',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wa_map (doctor_id, reminder_template_id),
  CONSTRAINT fk_wa_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id),
  CONSTRAINT fk_wa_rt FOREIGN KEY (reminder_template_id) REFERENCES reminder_templates(id)
) ENGINE=InnoDB;
