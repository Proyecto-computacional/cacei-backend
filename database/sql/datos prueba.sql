INSERT INTO cvs (cv_id, professor_number, update_date, professor_name, age, birth_date, actual_position, duration)
VALUES (1, 10285, '2025-03-15', 'Alberto', 40, '1985-03-15', 'Profesor', 10);

-- Insertar en la tabla users
INSERT INTO users (user_rpe, user_mail, user_role, user_name, cv_id)
VALUES ('10285', 'alberto@uaslp.edu.mx', 'ADMINISTRADOR', 'Alberto', 1);

-- Insertar en la tabla areas
INSERT INTO areas (area_id, area_name, user_rpe)
VALUES ('A01', 'Educación', '10285');

-- Insertar en la tabla careers
INSERT INTO careers (career_id, area_id, career_name, user_rpe)
VALUES ('C01', 'A01', 'Ingeniería Sistemas', '10285');

-- Insertar en la tabla frames_of_reference
INSERT INTO frames_of_reference (frame_id, frame_name)
VALUES (1, 'Marco de Referencia 2025');

-- Insertar en la tabla categories
INSERT INTO categories (category_id, category_name, frame_id, indice)
VALUES (1, 'Categoría Mantenimiento', 1, 1);

-- Insertar en la tabla sections
INSERT INTO sections (section_id, category_id, section_name, section_description, indice, is_Standard)
VALUES (1, 1, 'Uso y seguridad', 'Descripción de la sección 1', 1, TRUE);


-- Insertar en la tabla standards
INSERT INTO standards (standard_id, section_id, standard_name, standard_description, is_transversal, help, indice)
VALUES (1, 1, 'Criterio de seguridad', 'Descripción del estándar 1', FALSE, 'Ayuda para el estándar 1', 1);



-- Insertar en la tabla evidences
INSERT INTO evidences (evidence_id, standard_id, user_rpe, process_id, due_date)
VALUES (1, 1, '10285', 1, '2025-05-01'),
 (2, 1, '10285', 1, '2025-05-01'),
 (3, 1, '10285', 1, '2025-05-01');


-- Insertar en la tabla revisers
INSERT INTO revisers (reviser_id, user_rpe, evidence_id)
VALUES (1, '10285', 1),
(2, '10285', 2),
(3, '10285', 3);





-- Insertar en la tabla educations
INSERT INTO educations (education_id, cv_id, institution, degree_obtained, obtained_year, professional_license, degree_name)
VALUES (1, 1, 'UASLP', 'A', 2008, '12345678', 'Licenciado en Ingeniería en Sistemas Computacionales' );

-- Insertar en la tabla teacher_trainings
INSERT INTO teacher_trainings (teacher_training_id, title_certification, obtained_year, institution_country, hours, cv_id)
VALUES (1, 'Certificación en Docencia Universitaria', 2010, 'México', 30, 1);

-- Insertar en la tabla disciplinary_updates
INSERT INTO disciplinary_updates (disciplinary_update_id, cv_id, title_certification, year_certification, institution_country, hours)
VALUES (1, 1, 'Actualización Disciplinaria en Ética Profesional', 2015, 'México', 15);

-- Insertar en la tabla academic_managements
INSERT INTO academic_managements (academic_management_id, cv_id, job_position, institution, start_date, end_date)
VALUES (1, 1, 'Coordinador Académico', 'UASLP', '2015-06-01', '2020-06-01');

-- Insertar en la tabla academic_products
INSERT INTO academic_products (academic_product_id, cv_id, academic_product_number, description)
VALUES (1, 1, 101, 'Artículo de investigación en el área de Sistemas Inteligentes');

-- Insertar en la tabla laboral_experiences
INSERT INTO laboral_experiences (laboral_experience_id, cv_id, company_name, position, start_date, end_date)
VALUES (1, 1, 'Empresa Tesla', 'Desarrollador de Software', '2010-05-01', '2015-05-01');

-- Insertar en la tabla engineering_designs
INSERT INTO engineering_designs (engineering_design_id, cv_id, institution, period, level_experience)
VALUES (1, 1, 'UASLP', 3, 'Avanzado');

-- Insertar en la tabla professional_achievements
INSERT INTO professional_achievements (professional_achievement_id, cv_id, description)
VALUES (1, 1, 'Desarrollador principal de un sistema de gestión universitaria');

-- Insertar en la tabla participations
INSERT INTO participations (participation_id, cv_id, institution, period, level_participation)
VALUES (1, 1, 'UASLP', 2, 1);

-- Insertar en la tabla awards
INSERT INTO awards (award_id, cv_id, description)
VALUES (1, 1, 'Premio a la Innovación en Tecnología Educativa');

-- Insertar en la tabla contributions_to_pe
INSERT INTO contributions_to_pe (contribution_id, cv_id, description)
VALUES (1, 1, 'Contribución al diseño de plataformas de educación en línea');

-- Insertar en la tabla subjects
INSERT INTO subjects (subject_id, subject_name, career_id)
VALUES (1, 'Programación Avanzada', 'CA04');

-- Insertar en la tabla groups
INSERT INTO groups (group_id, semester, type_a, period_a, subject_id, hour_a)
VALUES (1, '2025-1', TRUE, 'Mañana', 1, '09:00');

-- Insertar en la tabla accreditation_processes
INSERT INTO accreditation_processes (process_id, career_id, frame_id, process_name, start_date, end_date, due_date, finished)
VALUES (1, 'C01', 1, 'Acreditación de Ingeniería en Sistemas', '2025-01-01', '2025-12-31', '2025-12-01', FALSE);

-- Insertar en la tabla statuses
INSERT INTO statuses (status_id, status_description, user_rpe, evidence_id, status_date, feedback)
VALUES (1, 'APROBADO', '10285', 1, '2025-04-22', 'Muy bueno'),
(2, 'NO APROBADO', '10285', 2, '2025-04-22', 'Malo'),
(3, 'PENDIENTE', '10285', 3, '2025-04-22', null);


-- Insertar en la tabla files
INSERT INTO files (file_id, file_url, upload_date, evidence_id, file_name)
VALUES (1, 'http://files.uaslp.edu.mx/evidences/1.pdf', '2025-04-22', 1, 'Evidencia_1.pdf');

-- Insertar en la tabla notifications
INSERT INTO notifications (notification_id, title, evidence_id, notification_date, user_rpe, reviser_id, description, seen, pinned, starred)
VALUES (1, 'Nueva Evidencia para Revisión', 1, '2025-04-22', '10285', '10285', 'Revisión pendiente de evidencia', FALSE, FALSE, FALSE);
