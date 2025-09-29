/*Nota: modificar restricciones y validaciones en controladores*/

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
    category_name VARCHAR(60) NOT NULL,
    frame_id BIGINT NOT NULL,
    indice INT NOT NULL, 
    PRIMARY KEY (category_id),
    FOREIGN KEY (frame_id) REFERENCES frames_of_reference(frame_id)
);

CREATE TABLE sections (
    section_id BIGSERIAL NOT NULL,
    category_id BIGINT NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    section_description VARCHAR(150) NOT NULL,
    indice INT NOT NULL,
    is_standard BOOL,
    PRIMARY KEY (section_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE standards (
    standard_id BIGSERIAL NOT NULL,
    section_id BIGINT NOT NULL,
    standard_name VARCHAR(50) NOT NULL,
    standard_description VARCHAR(150) NOT NULL,
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
    FOREIGN KEY (standard_id) REFERENCES standards(standard_id)
);

CREATE TABLE revisers (
    reviser_id BIGSERIAL NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    evidence_id BIGINT,
    PRIMARY KEY (reviser_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id)
);

CREATE TABLE educations (
    education_id BIGSERIAL NOT NULL,
    cv_id BIGINT NOT NULL,
    institution VARCHAR(70),
    degree_obtained VARCHAR(1),
    obtained_year INT,
    professional_license VARCHAR(30),
    degree_name VARCHAR(100),
    PRIMARY KEY (education_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE teacher_trainings (
    teacher_training_id BIGSERIAL NOT NULL,
    title_certification VARCHAR(50),
    obtained_year INT,
    institution_country VARCHAR(50),
    hours INT,
    cv_id BIGINT,
    PRIMARY KEY (teacher_training_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE disciplinary_updates (
    disciplinary_update_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    title_certification VARCHAR(100),
    year_certification INT,
    institution_country VARCHAR(50),
    hours INT,
    PRIMARY KEY (disciplinary_update_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE academic_managements (
    academic_management_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    job_position VARCHAR(100),
    institution VARCHAR(70),
    start_date VARCHAR(10),
    end_date VARCHAR(10),
    PRIMARY KEY (academic_management_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE academic_products (
    academic_product_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    academic_product_number INT,
    description VARCHAR(150),
    PRIMARY KEY (academic_product_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE laboral_experiences (
    laboral_experience_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    company_name VARCHAR(60),
    position VARCHAR(60),
    start_date VARCHAR(10),
    end_date VARCHAR(10),
    PRIMARY KEY (laboral_experience_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE engineering_designs (
    engineering_design_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(70),
    period INT,
    level_experience VARCHAR(20),
    PRIMARY KEY (engineering_design_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE professional_achievements (
    achievement_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(500),
    PRIMARY KEY (achievement_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE participations (
    participation_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(70),
    period INT,
    level_participation VARCHAR(20),
    PRIMARY KEY (participation_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE awards (
    award_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(255),
    PRIMARY KEY (award_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE contributions_to_pe (
    contribution_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    description VARCHAR(500),
    PRIMARY KEY (contribution_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE areas (
    area_id VARCHAR(20) NOT NULL,
    area_name VARCHAR(60) NOT NULL,
    user_rpe VARCHAR(20),
    PRIMARY KEY (area_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);


CREATE TABLE users (
    user_rpe VARCHAR(20) NOT NULL,
    user_mail VARCHAR(100) UNIQUE NOT NULL,
    user_role VARCHAR(30) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    user_area VARCHAR(100) NOT NULL,
    cv_id BIGINT,
    situation VARCHAR(20),
    PRIMARY KEY (user_rpe),
    FOREIGN KEY (user_area) REFERENCES areas(area_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE careers (
    career_id INT NOT NULL,
    area_id INT NOT NULL,
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
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
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
    reviser_id VARCHAR(20) NOT NULL, 
    description VARCHAR(255),
    seen BOOL NOT NULL,
    pinned BOOL NOT NULL DEFAULT FALSE,
    starred BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id),
    FOREIGN KEY (reviser_id) REFERENCES users(user_rpe)
);

INSERT INTO role(role_id, role_name)VALUES
(1, 'ADMINISTRADOR'),
(2, 'JEFE DE AREA'),
(3, 'COORDINADOR'),
(4, 'PROFESOR'),
(5, 'DIRECTIVO'),
(6, 'DEPARTAMENTO UNIVERSITARIO');
(7, 'PERSONAL DE APOYO');

INSERT INTO permissions(permission_id, permission_name)VALUES
(1, 'Subir archivos'),
(2, 'Actualizar archivos'),
(3, 'Descargar archivos'),
(4, 'Eliminar archivos');


INSERT INTO role_permissions (role_id, permission_id, is_enabled) VALUES
(1, 1, true),
(1, 2, true),
(1, 3, true),
(1, 4, true),

(2, 1, true),
(2, 2, true),
(2, 3, true),
(2, 4, true),

(3, 1, true),
(3, 2, true),
(3, 3, true),
(3, 4, true),

(4, 1, true),
(4, 2, true),
(4, 3, true),
(4, 4, true),

(5, 1, false),
(5, 2, false),
(5, 3, true),
(5, 4, false),

(6, 1, true),
(6, 2, true),
(6, 3, true),
(6, 4, true).

(7, 1, true),
(7, 2, true),
(7, 3, true),
(7, 4, true);



INSERT INTO areas (area_id, area_name) VALUES
('7', 'Área Agroindustrial'),
('2', 'Área de Ciencias de la Computación'),
('AR03', 'Área de Ciencias de la Tierra'),
('3', 'Área Civil'),
('5', 'Área Mecánica y Eléctrica'),
('AR06', 'Área de Metalurgia y Materiales');

INSERT INTO careers (career_id, area_id, career_name) VALUES
('CA01', '7', 'Ingeniería Agroindustrial'),
('CA02', 'AR03', 'Ingeniería Ambiental'),
('CA03', '3', 'Ingeniería Civil'),
('CA04', '2', 'Ingeniería en Computación'),
('CA05', '5', 'Ingeniería en Electricidad y Automatización'),
('CA06', 'AR03', 'Ingeniería en Geología'),
('CA07', '2', 'Ingeniería en Sistemas Inteligentes'),
('CA08', '3', 'Ingeniería en Topografía y Construcción'),
('CA09', '5', 'Ingeniería Mecánica'),
('CA10', '5', 'Ingeniería Mecánica Administrativa'),
('CA11', '5', 'Ingeniería Mecánica Eléctrica'),
('CA12', '5', 'Ingeniería Mecatrónica'),
('CA13', 'AR06', 'Ingeniería Metalúrgica y de Materiales'),
('CA14', 'AR04', 'Ingeniería Geoinformática');