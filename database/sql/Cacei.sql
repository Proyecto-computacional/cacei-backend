CREATE TABLE cvs (
    cv_id BIGSERIAL NOT NULL,
    professor_number INT UNIQUE,
    update_date DATE,
    professor_name VARCHAR(50),
    age INT,
    birth_date DATE,
    actual_position VARCHAR(25),
    duration INT,
    PRIMARY KEY (cv_id)
);



CREATE TABLE permissions (
    permission_id SERIAL PRIMARY KEY,
    permission_name VARCHAR(50) UNIQUE NOT NULL
);


CREATE TABLE users (
    user_rpe VARCHAR(20) NOT NULL,
    user_mail VARCHAR(100) UNIQUE NOT NULL,
    user_role VARCHAR(30) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    cv_id BIGINT,
    situation VARCHAR(20),
    PRIMARY KEY (user_rpe),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE role_permissions (
   
    user_rpe VARCHAR(20) NOT NULL,
    permission_id INT NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (user_rpe, permission_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id)
);

CREATE TABLE frames_of_reference (
    frame_id INT NOT NULL,
    frame_name VARCHAR(60) NOT NULL,
    PRIMARY KEY (frame_id)
);

CREATE TABLE categories (
    category_id INT NOT NULL,
    category_name VARCHAR(60) NOT NULL,
    frame_id INT NOT NULL,
    indice INT NOT NULL, 
    PRIMARY KEY (category_id),
    FOREIGN KEY (frame_id) REFERENCES frames_of_reference(frame_id)
);

CREATE TABLE sections (
    section_id INT NOT NULL,
    category_id INT NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    section_description VARCHAR(150) NOT NULL,
    indice INT NOT NULL,
    is_standard BOOL,
    PRIMARY KEY (section_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE standards (
    standard_id INT NOT NULL,
    section_id INT NOT NULL,
    standard_name VARCHAR(50) NOT NULL,
    standard_description VARCHAR(150) NOT NULL,
    is_transversal BOOL NOT NULL,
    help VARCHAR(255),
    indice INT NOT NULL,
    PRIMARY KEY (standard_id),
    FOREIGN KEY (section_id) REFERENCES sections(section_id)
);

CREATE TABLE evidences (
    evidence_id INT NOT NULL,
    standard_id INT NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    group_id INT,
    process_id INT NOT NULL,
    due_date DATE NOT NULL,
    justification VARCHAR(1024),
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
    institution VARCHAR(30),
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
    title_certification VARCHAR(50),
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
    institution VARCHAR(50),
    start_date VARCHAR(7),
    end_date VARCHAR(7),
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
    start_date VARCHAR(7),
    end_date VARCHAR(7),
    PRIMARY KEY (laboral_experience_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE engineering_designs (
    engineering_design_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(30),
    period INT,
    level_experience VARCHAR(20),
    PRIMARY KEY (engineering_design_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE professional_achievements (
    achievement_id INT NOT NULL,
    cv_id BIGINT,
    description VARCHAR(500),
    PRIMARY KEY (achievement_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
);

CREATE TABLE participations (
    participation_id BIGSERIAL NOT NULL,
    cv_id BIGINT,
    institution VARCHAR(30),
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

CREATE TABLE careers (
    career_id VARCHAR(20) NOT NULL,
    area_id VARCHAR(20) NOT NULL,
    career_name VARCHAR(60) NOT NULL,
    user_rpe VARCHAR(20),
    PRIMARY KEY (career_id),
    FOREIGN KEY (area_id) REFERENCES areas(area_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);

CREATE TABLE accreditation_processes (
    process_id INT NOT NULL,
    career_id VARCHAR(20) NOT NULL,
    frame_id INT,
    process_name VARCHAR(150) NOT NULL,
    start_date DATE,
    end_date DATE,
    due_date DATE,
    finished BOOLEAN NOT NULL,
    PRIMARY KEY (process_id),
    FOREIGN KEY (career_id) REFERENCES careers(career_id),
    FOREIGN KEY (frame_id) REFERENCES frames_of_reference(frame_id)
);

CREATE TABLE subjects (
    subject_id INT NOT NULL,
    subject_name VARCHAR(50) NOT NULL,
    career_id VARCHAR(20) NOT NULL,
    PRIMARY KEY (subject_id),
    FOREIGN KEY (career_id) REFERENCES careers(career_id)
);

CREATE TABLE groups (
    group_id INT NOT NULL,
    semester VARCHAR(15) NOT NULL,
    type_a BOOL NOT NULL,
    period_a VARCHAR(25) NOT NULL,
    subject_id INT NOT NULL,
    hour_a VARCHAR(5) NOT NULL,
    PRIMARY KEY (group_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

CREATE TABLE statuses (
    status_id BIGSERIAL NOT NULL,
    status_description VARCHAR(30) NOT NULL,
    user_rpe VARCHAR(20) NOT NULL,
    evidence_id INT NOT NULL,
    status_date DATE NOT NULL,
    feedback VARCHAR(255),
    PRIMARY KEY (status_id),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe)
);

CREATE TABLE files (
    file_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    evidence_id INT NOT NULL,
    file_name VARCHAR(50),
    PRIMARY KEY (file_id),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id)
);

CREATE TABLE notifications (
    notification_id BIGSERIAL NOT NULL,
    title VARCHAR(30) NOT NULL,
    evidence_id INT,
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

INSERT INTO areas (area_id, area_name) VALUES
('AR01', 'Área Agroindustrial'),
('AR02', 'Área de Ciencias de la Computación'),
('AR03', 'Área de Ciencias de la Tierra'),
('AR04', 'Área Civil'),
('AR05', 'Área Mecánica y Eléctrica'),
('AR06', 'Área de Metalurgia y Materiales');

INSERT INTO careers (career_id, area_id, career_name) VALUES
('CA01', 'AR01', 'Ingeniería Agroindustrial'),
('CA02', 'AR03', 'Ingeniería Ambiental'),
('CA03', 'AR04', 'Ingeniería Civil'),
('CA04', 'AR02', 'Ingeniería en Computación'),
('CA05', 'AR05', 'Ingeniería en Electricidad y Automatización'),
('CA06', 'AR03', 'Ingeniería en Geología'),
('CA07', 'AR02', 'Ingeniería en Sistemas Inteligentes'),
('CA08', 'AR04', 'Ingeniería en Topografía y Construcción'),
('CA09', 'AR05', 'Ingeniería Mecánica'),
('CA10', 'AR05', 'Ingeniería Mecánica Administrativa'),
('CA11', 'AR05', 'Ingeniería Mecánica Eléctrica'),
('CA12', 'AR05', 'Ingeniería Mecatrónica'),
('CA13', 'AR06', 'Ingeniería Metalúrgica y de Materiales'),
('CA14', 'AR04', 'Ingeniería Geoinformática');