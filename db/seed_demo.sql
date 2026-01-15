-- Seed demo data
USE doctor_saas;

INSERT IGNORE INTO doctors (full_name, email, password_hash, specialty, phone, is_active) VALUES ('Dr Demo', 'demo@exemple.fr', '$2y$10$j2iBbRAk6fCmALs6ld.9betGUWFbGmAF55wblqmQSLUuEI5BUZEZi', 'Generaliste', '22110000', 1);

SET @doc_id = (SELECT id FROM doctors WHERE email = 'demo@exemple.fr');

INSERT IGNORE INTO services (doctor_id, name, default_price, default_duration_minutes, description, is_active) VALUES
(@doc_id, 'Consultation', 40.000, 30, 'Consultation standard', 1),
(@doc_id, 'Controle', 25.000, 20, 'Controle rapide', 1),
(@doc_id, 'Injection', 30.000, 15, 'Injection simple', 1),
(@doc_id, 'Teleconsultation', 35.000, 25, 'Teleconsultation', 1),
(@doc_id, 'Certificat medical', 20.000, 10, 'Certificat', 1);

INSERT INTO patients (doctor_id, external_id, full_name, phone, email, dob, notes) VALUES
(@doc_id, 'p1', 'Amina Ben Salah', '55110001', 'amina@example.tn', '1991-02-12', ''),
(@doc_id, 'p2', 'Youssef Trabelsi', '55110002', 'youssef@example.tn', '1985-05-22', ''),
(@doc_id, 'p3', 'Khadija Miled', '55110003', 'khadija@example.tn', '1994-01-08', ''),
(@doc_id, 'p4', 'Sami Gharbi', '55110004', 'sami@example.tn', '1978-09-17', ''),
(@doc_id, 'p5', 'Noura Ben Ali', '55110005', 'noura@example.tn', '1990-03-30', ''),
(@doc_id, 'p6', 'Hichem Oueslati', '55110006', 'hichem@example.tn', '1982-11-11', ''),
(@doc_id, 'p7', 'Sana Jaziri', '55110007', 'sana@example.tn', '1996-07-19', ''),
(@doc_id, 'p8', 'Mohamed Ayari', '55110008', 'mohamed@example.tn', '1988-12-02', ''),
(@doc_id, 'p9', 'Leila Mhiri', '55110009', 'leila@example.tn', '1993-06-25', ''),
(@doc_id, 'p10', 'Rim Saidi', '55110010', 'rim@example.tn', '1992-04-14', ''),
(@doc_id, 'p11', 'Karim Zribi', '55110011', 'karim@example.tn', '1986-08-09', ''),
(@doc_id, 'p12', 'Ines Ben Youssef', '55110012', 'ines@example.tn', '1998-10-05', ''),
(@doc_id, 'p13', 'Bilel Kammoun', '55110013', 'bilel@example.tn', '1975-02-27', ''),
(@doc_id, 'p14', 'Marwa Chouchene', '55110014', 'marwa@example.tn', '1989-01-21', ''),
(@doc_id, 'p15', 'Walid Baccouche', '55110015', 'walid@example.tn', '1997-09-13', '');

INSERT INTO appointments (doctor_id, external_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES
(@doc_id, 'a1', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p1'), '2026-01-02 09:00:00', '2026-01-02 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a2', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p2'), '2026-01-02 10:00:00', '2026-01-02 10:20:00', 'TERMINE', 'Controle', ''),
(@doc_id, 'a3', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), '2026-01-03 11:00:00', '2026-01-03 11:30:00', 'CONFIRME', 'Consultation', ''),
(@doc_id, 'a4', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p4'), '2026-01-03 12:00:00', '2026-01-03 12:15:00', 'NO_SHOW', 'Injection', ''),
(@doc_id, 'a5', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), '2026-01-04 09:30:00', '2026-01-04 09:50:00', 'ANNULE', 'Controle', ''),
(@doc_id, 'a6', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), '2026-01-04 10:30:00', '2026-01-04 11:00:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a7', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), '2026-01-05 09:00:00', '2026-01-05 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a8', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p8'), '2026-01-05 11:00:00', '2026-01-05 11:15:00', 'TERMINE', 'Injection', ''),
(@doc_id, 'a9', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p9'), '2026-01-06 09:00:00', '2026-01-06 09:30:00', 'CONFIRME', 'Consultation', ''),
(@doc_id, 'a10', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p10'), '2026-01-06 10:30:00', '2026-01-06 10:55:00', 'TERMINE', 'Teleconsultation', ''),
(@doc_id, 'a11', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p11'), '2026-01-07 09:00:00', '2026-01-07 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a12', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p12'), '2026-01-07 10:30:00', '2026-01-07 10:50:00', 'TERMINE', 'Controle', ''),
(@doc_id, 'a13', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p13'), '2026-01-08 09:30:00', '2026-01-08 09:45:00', 'NO_SHOW', 'Injection', ''),
(@doc_id, 'a14', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p14'), '2026-01-08 11:00:00', '2026-01-08 11:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a15', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p15'), '2026-01-09 09:00:00', '2026-01-09 09:30:00', 'CONFIRME', 'Consultation', ''),
(@doc_id, 'a16', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p1'), '2026-01-10 09:00:00', '2026-01-10 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a17', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p2'), '2026-01-10 10:30:00', '2026-01-10 10:50:00', 'TERMINE', 'Controle', ''),
(@doc_id, 'a18', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), '2026-01-11 09:30:00', '2026-01-11 10:00:00', 'ANNULE', 'Consultation', ''),
(@doc_id, 'a19', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p4'), '2026-01-11 11:00:00', '2026-01-11 11:20:00', 'TERMINE', 'Controle', ''),
(@doc_id, 'a20', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), '2026-01-12 09:00:00', '2026-01-12 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a21', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), '2026-01-12 10:30:00', '2026-01-12 10:55:00', 'TERMINE', 'Teleconsultation', ''),
(@doc_id, 'a22', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), '2026-01-13 09:00:00', '2026-01-13 09:20:00', 'NO_SHOW', 'Controle', ''),
(@doc_id, 'a23', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p8'), '2026-01-13 10:00:00', '2026-01-13 10:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a24', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p9'), '2026-01-14 09:00:00', '2026-01-14 09:15:00', 'TERMINE', 'Injection', ''),
(@doc_id, 'a25', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p10'), '2026-01-14 11:00:00', '2026-01-14 11:30:00', 'CONFIRME', 'Consultation', ''),
(@doc_id, 'a26', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p11'), '2026-01-15 09:00:00', '2026-01-15 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a27', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p12'), '2026-01-15 10:00:00', '2026-01-15 10:20:00', 'TERMINE', 'Controle', ''),
(@doc_id, 'a28', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p13'), '2026-01-16 09:00:00', '2026-01-16 09:30:00', 'TERMINE', 'Consultation', ''),
(@doc_id, 'a29', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p14'), '2026-01-16 10:00:00', '2026-01-16 10:30:00', 'ANNULE', 'Consultation', ''),
(@doc_id, 'a30', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p15'), '2026-01-17 09:00:00', '2026-01-17 09:15:00', 'TERMINE', 'Certificat', '');

INSERT INTO appointment_services (appointment_id, service_id, qty) VALUES
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a1'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a2'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Controle'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a3'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a4'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Injection'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a6'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a7'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a8'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Injection'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a10'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Teleconsultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a11'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 1),
((SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a12'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Controle'), 1);

INSERT INTO payments (doctor_id, external_id, patient_id, appointment_id, received_at, amount, receipt_number, reference, notes) VALUES
(@doc_id, 'pay1', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p1'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a1'), '2026-01-02', 40.000, 'RC-2026-000101', 'R1', ''),
(@doc_id, 'pay2', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p2'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a2'), '2026-01-02', 25.000, 'RC-2026-000102', 'R2', ''),
(@doc_id, 'pay3', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a3'), '2026-01-03', 40.000, 'RC-2026-000103', 'R3', ''),
(@doc_id, 'pay4', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a6'), '2026-01-04', 40.000, 'RC-2026-000104', 'R4', ''),
(@doc_id, 'pay5', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a7'), '2026-01-05', 40.000, 'RC-2026-000105', 'R5', ''),
(@doc_id, 'pay6', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p8'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a8'), '2026-01-05', 30.000, 'RC-2026-000106', 'R6', ''),
(@doc_id, 'pay7', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p10'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a10'), '2026-01-06', 35.000, 'RC-2026-000107', 'R7', ''),
(@doc_id, 'pay8', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p11'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a11'), '2026-01-07', 40.000, 'RC-2026-000108', 'R8', ''),
(@doc_id, 'pay9', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p12'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a12'), '2026-01-07', 25.000, 'RC-2026-000109', 'R9', ''),
(@doc_id, 'pay10', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p14'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a14'), '2026-01-08', 40.000, 'RC-2026-000110', 'R10', ''),
(@doc_id, 'pay11', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p1'), NULL, '2026-01-09', 20.000, 'RC-2026-000111', 'R11', 'Sans RDV'),
(@doc_id, 'pay12', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p2'), NULL, '2026-01-09', 30.000, 'RC-2026-000112', 'R12', 'Sans RDV'),
(@doc_id, 'pay13', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a20'), '2026-01-12', 40.000, 'RC-2026-000113', 'R13', ''),
(@doc_id, 'pay14', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a21'), '2026-01-12', 35.000, 'RC-2026-000114', 'R14', ''),
(@doc_id, 'pay15', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p8'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a23'), '2026-01-13', 40.000, 'RC-2026-000115', 'R15', ''),
(@doc_id, 'pay16', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p9'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a24'), '2026-01-14', 30.000, 'RC-2026-000116', 'R16', ''),
(@doc_id, 'pay17', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p11'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a26'), '2026-01-15', 40.000, 'RC-2026-000117', 'R17', ''),
(@doc_id, 'pay18', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p12'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a27'), '2026-01-15', 25.000, 'RC-2026-000118', 'R18', ''),
(@doc_id, 'pay19', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p13'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a28'), '2026-01-16', 40.000, 'RC-2026-000119', 'R19', ''),
(@doc_id, 'pay20', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p15'), (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a30'), '2026-01-17', 20.000, 'RC-2026-000120', 'R20', ''),
(@doc_id, 'pay21', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), NULL, '2026-01-17', 15.000, 'RC-2026-000121', 'R21', 'Sans RDV'),
(@doc_id, 'pay22', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p4'), NULL, '2026-01-18', 20.000, 'RC-2026-000122', 'R22', 'Sans RDV'),
(@doc_id, 'pay23', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), NULL, '2026-01-18', 25.000, 'RC-2026-000123', 'R23', 'Sans RDV'),
(@doc_id, 'pay24', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), NULL, '2026-01-19', 30.000, 'RC-2026-000124', 'R24', 'Sans RDV'),
(@doc_id, 'pay25', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), NULL, '2026-01-19', 35.000, 'RC-2026-000125', 'R25', 'Sans RDV'),
(@doc_id, 'pay26', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p8'), NULL, '2026-01-20', 40.000, 'RC-2026-000126', 'R26', 'Sans RDV'),
(@doc_id, 'pay27', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p9'), NULL, '2026-01-20', 22.000, 'RC-2026-000127', 'R27', 'Sans RDV'),
(@doc_id, 'pay28', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p10'), NULL, '2026-01-21', 18.000, 'RC-2026-000128', 'R28', 'Sans RDV'),
(@doc_id, 'pay29', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p11'), NULL, '2026-01-21', 45.000, 'RC-2026-000129', 'R29', 'Sans RDV'),
(@doc_id, 'pay30', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p12'), NULL, '2026-01-22', 28.000, 'RC-2026-000130', 'R30', 'Sans RDV'),
(@doc_id, 'pay31', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p13'), NULL, '2026-01-22', 32.000, 'RC-2026-000131', 'R31', 'Sans RDV'),
(@doc_id, 'pay32', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p14'), NULL, '2026-01-23', 27.000, 'RC-2026-000132', 'R32', 'Sans RDV'),
(@doc_id, 'pay33', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p15'), NULL, '2026-01-23', 19.000, 'RC-2026-000133', 'R33', 'Sans RDV'),
(@doc_id, 'pay34', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p1'), NULL, '2026-01-24', 33.000, 'RC-2026-000134', 'R34', 'Sans RDV'),
(@doc_id, 'pay35', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p2'), NULL, '2026-01-24', 26.000, 'RC-2026-000135', 'R35', 'Sans RDV'),
(@doc_id, 'pay36', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), NULL, '2026-01-25', 24.000, 'RC-2026-000136', 'R36', 'Sans RDV'),
(@doc_id, 'pay37', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p4'), NULL, '2026-01-25', 31.000, 'RC-2026-000137', 'R37', 'Sans RDV'),
(@doc_id, 'pay38', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), NULL, '2026-01-26', 29.000, 'RC-2026-000138', 'R38', 'Sans RDV'),
(@doc_id, 'pay39', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p6'), NULL, '2026-01-26', 21.000, 'RC-2026-000139', 'R39', 'Sans RDV'),
(@doc_id, 'pay40', (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), NULL, '2026-01-27', 34.000, 'RC-2026-000140', 'R40', 'Sans RDV');

INSERT INTO payment_items (payment_id, service_id, description, qty, unit_price, line_total) VALUES
((SELECT id FROM payments WHERE doctor_id = @doc_id AND external_id = 'pay1'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Consultation'), 'Consultation', 1, 40.000, 40.000),
((SELECT id FROM payments WHERE doctor_id = @doc_id AND external_id = 'pay2'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Controle'), 'Controle', 1, 25.000, 25.000),
((SELECT id FROM payments WHERE doctor_id = @doc_id AND external_id = 'pay6'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Injection'), 'Injection', 1, 30.000, 30.000),
((SELECT id FROM payments WHERE doctor_id = @doc_id AND external_id = 'pay7'), (SELECT id FROM services WHERE doctor_id = @doc_id AND name = 'Teleconsultation'), 'Teleconsultation', 1, 35.000, 35.000);

INSERT INTO reminder_templates (doctor_id, name, channel, body, is_active) VALUES
(@doc_id, 'Rappel standard', 'SMS', 'Bonjour {patient_nom}, votre RDV est prevu le {date} a {heure}.', 1),
(@doc_id, 'Rappel email', 'EMAIL', 'Bonjour {patient_nom}, merci de confirmer votre RDV du {date} a {heure}.', 1);

INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES
(@doc_id, (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a3'), (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p3'), (SELECT id FROM reminder_templates WHERE doctor_id = @doc_id AND name = 'Rappel standard'), 'SMS', 'Rappel envoye', 'SENT', '2026-01-02 18:00:00', NULL),
(@doc_id, (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a7'), (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p7'), (SELECT id FROM reminder_templates WHERE doctor_id = @doc_id AND name = 'Rappel standard'), 'SMS', 'Rappel envoye', 'SENT', '2026-01-05 18:00:00', NULL),
(@doc_id, (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a10'), (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p10'), (SELECT id FROM reminder_templates WHERE doctor_id = @doc_id AND name = 'Rappel email'), 'EMAIL', 'Rappel envoye', 'SENT', '2026-01-06 18:00:00', NULL),
(@doc_id, (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a15'), (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p15'), (SELECT id FROM reminder_templates WHERE doctor_id = @doc_id AND name = 'Rappel standard'), 'SMS', 'Rappel envoye', 'DRAFT', '2026-01-08 18:00:00', NULL),
(@doc_id, (SELECT id FROM appointments WHERE doctor_id = @doc_id AND external_id = 'a20'), (SELECT id FROM patients WHERE doctor_id = @doc_id AND external_id = 'p5'), (SELECT id FROM reminder_templates WHERE doctor_id = @doc_id AND name = 'Rappel standard'), 'SMS', 'Rappel envoye', 'SENT', '2026-01-12 18:00:00', NULL);

INSERT INTO cash_closures (doctor_id, closure_date, total_system, cash_declared, difference, notes) VALUES
(@doc_id, '2026-01-05', 175.000, 175.000, 0.000, 'Cloture'),
(@doc_id, '2026-01-12', 165.000, 165.000, 0.000, 'Cloture');