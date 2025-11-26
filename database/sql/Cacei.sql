/*Nota: modificar restricciones y validaciones en controladores*/

SET TIMEZONE = 'America/Mexico_City';

CREATE TABLE cvs (
    cv_id BIGSERIAL NOT NULL,
    professor_number INT,
    update_date DATE,
    professor_name VARCHAR(150),
    age INT,
    birth_date DATE,
    actual_position VARCHAR(40),
    duration INT,
    PRIMARY KEY (cv_id)
);

CREATE TABLE areas (
    area_id VARCHAR(20) NOT NULL,
    area_name VARCHAR(60) NOT NULL,
    user_rpe VARCHAR(20),  -- Esta FK se agregará después
    PRIMARY KEY (area_id)
);

-- Ahora crear users (sin la FK problemática temporalmente)
CREATE TABLE users (
    user_rpe VARCHAR(20) NOT NULL,
    user_mail VARCHAR(100) UNIQUE NOT NULL,
    user_role VARCHAR(30) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    user_area VARCHAR(100) NOT NULL,
    cv_id BIGINT,
    situation VARCHAR(20),
    PRIMARY KEY (user_rpe),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
    -- QUITAR temporalmente: FOREIGN KEY (user_area) REFERENCES areas(area_id)
);

-- Ahora agregar la FK faltante a users
ALTER TABLE users ADD CONSTRAINT fk_user_area 
    FOREIGN KEY (user_area) REFERENCES areas(area_id);

-- Y agregar la FK a areas
ALTER TABLE areas ADD CONSTRAINT fk_area_user 
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe);



CREATE TABLE permissions (
    permission_id SERIAL PRIMARY KEY,
    permission_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE role (
    role_id INT NOT NULL,
    role_name VARCHAR (30) NOT NULL,
    PRIMARY KEY (role_id)
);

CREATE TABLE role_permissions (
    role_id int NOT NULL,
    permission_id INT NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id)
);

CREATE TABLE frames_of_reference (
    frame_id BIGSERIAL NOT NULL,
    frame_name VARCHAR(60) NOT NULL,
    PRIMARY KEY (frame_id)
);

CREATE TABLE categories (
    category_id BIGSERIAL NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    frame_id BIGINT NOT NULL,
    indice INT NOT NULL, 
    PRIMARY KEY (category_id),
    FOREIGN KEY (frame_id) REFERENCES frames_of_reference(frame_id)
);

CREATE TABLE sections (
    section_id BIGSERIAL NOT NULL,
    category_id BIGINT NOT NULL,
    section_name VARCHAR(1000) NOT NULL,
    section_description VARCHAR(1500) NOT NULL,
    indice INT NOT NULL,
    is_standard BOOL,
    PRIMARY KEY (section_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE standards (
    standard_id BIGSERIAL NOT NULL,
    section_id BIGINT NOT NULL,
    standard_name VARCHAR(100) NOT NULL,
    standard_description VARCHAR(1500) NOT NULL,
    is_transversal BOOL NOT NULL,
    help VARCHAR(255),
    indice INT NOT NULL,
    PRIMARY KEY (standard_id),
    FOREIGN KEY (section_id) REFERENCES sections(section_id)
);

CREATE TABLE evidences (
    evidence_id BIGSERIAL NOT NULL,
    standard_id BIGINT NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    process_id INT NOT NULL,
    due_date DATE NOT NULL,
    justification VARCHAR(2048),
    PRIMARY KEY (evidence_id),
    FOREIGN KEY (standard_id) REFERENCES standards(standard_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);

CREATE TABLE revisers (
    reviser_id BIGSERIAL NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    evidence_id INT,
    PRIMARY KEY (reviser_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id)
);

CREATE TABLE educations (
    education_id BIGSERIAL NOT NULL,
    cv_id BIGINT NOT NULL,
    institution VARCHAR(150),
    degree_obtained VARCHAR(1),
    obtained_year INT,
    professional_license VARCHAR(50),
    degree_name VARCHAR(150),
    PRIMARY KEY (education_id)
);

CREATE TABLE teacher_trainings (
    teacher_training_id BIGSERIAL NOT NULL,
    title_certification VARCHAR(200),
    obtained_year INT,
    institution_country VARCHAR(150),
    hours INT,
    cv_id BIGINT,
    PRIMARY KEY (teacher_training_id)
);

CREATE TABLE disciplinary_updates (
    disciplinary_update_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    title_certification VARCHAR(260),
    year_certification INT,
    institution_country VARCHAR(50),
    hours INT,
    PRIMARY KEY (disciplinary_update_id)
);

CREATE TABLE academic_managements (
    academic_management_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    job_position VARCHAR(250),
    institution VARCHAR(200),
    start_date VARCHAR(10),
    end_date VARCHAR(10),
    PRIMARY KEY (academic_management_id)

);

CREATE TABLE academic_products (
    academic_product_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(300),
    PRIMARY KEY (academic_product_id)

);

CREATE TABLE laboral_experiences (
    laboral_experience_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    company_name VARCHAR(160),
    position VARCHAR(270),
    start_date VARCHAR(10),
    end_date VARCHAR(10),
    PRIMARY KEY (laboral_experience_id)

);

CREATE TABLE engineering_designs (
    engineering_design_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(250),
    period INT,
    level_experience VARCHAR(40),
    PRIMARY KEY (engineering_design_id)
);

CREATE TABLE professional_achievements (
    professional_achievement_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(500),
    PRIMARY KEY (professional_achievement_id)

);

CREATE TABLE participations (
    participation_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(270),
    period INT,
    level_participation VARCHAR(70),
    PRIMARY KEY (participation_id)

);

CREATE TABLE awards (
    award_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(300),
    PRIMARY KEY (award_id)

);

CREATE TABLE contributions_to_pe (
    contribution_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(500),
    PRIMARY KEY (contribution_id)

);

CREATE TABLE careers (
    career_id INT NOT NULL,
    area_id VARCHAR(20) NOT NULL,
    career_name VARCHAR(60) NOT NULL,
    user_rpe VARCHAR(20),
    PRIMARY KEY (career_id),
    FOREIGN KEY (area_id) REFERENCES areas(area_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);

CREATE TABLE accreditation_processes (
    process_id BIGSERIAL NOT NULL,
    career_id INT NOT NULL,
    frame_id BIGINT,
    process_name VARCHAR(150) NOT NULL,
    start_date DATE,
    end_date DATE,
    due_date DATE,
    finished BOOLEAN NOT NULL,
    deleted BOOLEAN NOT NULL,
    PRIMARY KEY (process_id),
    FOREIGN KEY (career_id) REFERENCES careers(career_id),
    FOREIGN KEY (frame_id) REFERENCES frames_of_reference(frame_id)
);

CREATE TABLE statuses (
    status_id BIGSERIAL NOT NULL,
    status_description VARCHAR(30) NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    evidence_id BIGINT NOT NULL,
    status_date TIMESTAMP NOT NULL,
    --created_at TIMESTAMP,
    --updated_at TIMESTAMP,
    feedback VARCHAR(255),
    PRIMARY KEY (status_id),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);

CREATE TABLE files (
    file_id BIGSERIAL NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    evidence_id BIGINT NOT NULL,
    file_name VARCHAR(50),
    PRIMARY KEY (file_id),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id)
);

CREATE TABLE notifications (
    notification_id BIGSERIAL NOT NULL,
    title VARCHAR(50) NOT NULL,
    evidence_id BIGINT,
    notification_date DATE NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    reviser_id VARCHAR(20) NOT NULL, -- ahora apunta a user_rpe
    description VARCHAR(255),
    seen BOOL NOT NULL,
    pinned BOOL NOT NULL DEFAULT FALSE,
    starred BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id),
    FOREIGN KEY (reviser_id) REFERENCES users(user_rpe)
);



INSERT INTO role(role_id, role_name) VALUES
(1, 'ADMINISTRADOR'),
(2, 'JEFE DE AREA'),
(3, 'COORDINADOR DE CARRERA'),
(4, 'PROFESOR'),
(5, 'DIRECTIVO'),
(6, 'DEPARTAMENTO UNIVERSITARIO'),
(7, 'PERSONAL DE APOYO');

INSERT INTO permissions(permission_id, permission_name) VALUES
(1, 'Subir archivos'),
(2, 'Actualizar archivos'),
(3, 'Descargar archivos'),
(4, 'Eliminar archivos');

INSERT INTO role_permissions (role_id, permission_id, is_enabled) VALUES
(1, 1, true), (1, 2, true), (1, 3, true), (1, 4, true),
(2, 1, true), (2, 2, true), (2, 3, true), (2, 4, true),
(3, 1, true), (3, 2, true), (3, 3, true), (3, 4, true),
(4, 1, true), (4, 2, true), (4, 3, true), (4, 4, true),
(5, 1, false), (5, 2, false), (5, 3, true), (5, 4, false),
(6, 1, true), (6, 2, true), (6, 3, true), (6, 4, true),
(7, 1, true), (7, 2, true), (7, 3, true), (7, 4, true);

INSERT INTO areas (area_id, area_name) VALUES
('7', 'Área Agroindustrial'),
('2', 'Área de Ciencias de la Computación'),
('4', 'Área de Ciencias de la Tierra'),
('3', 'Área Civil'),
('5', 'Área Mecánica y Eléctrica'),
('6', 'Área de Metalurgia y Materiales');

INSERT INTO careers (career_id, area_id, career_name) VALUES
(1, '7', 'Ingeniería Agroindustrial'),
(2, '4', 'Ingeniería Ambiental'),
(3, '3', 'Ingeniería Civil'),
(4, '2', 'Ingeniería en Computación'),
(5, '5', 'Ingeniería en Electricidad y Automatización'),
(6, '4', 'Ingeniería en Geología'),
(7, '2', 'Ingeniería en Sistemas Inteligentes'),
(8, '3', 'Ingeniería en Topografía y Construcción'),
(9, '5', 'Ingeniería Mecánica'),
(10, '5', 'Ingeniería Mecánica Administrativa'),
(11, '5', 'Ingeniería Mecánica Eléctrica'),
(12, '5', 'Ingeniería Mecatrónica'),
(13, '6', 'Ingeniería Metalúrgica y de Materiales');
