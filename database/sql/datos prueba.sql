SET TIMEZONE = 'America/Mexico_City';

CREATE TABLE role (
    role_id SERIAL PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE permissions (
    permission_id SERIAL PRIMARY KEY,
    permission_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id)
);

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

CREATE TABLE frames_of_reference (
    frame_id INT NOT NULL,
    frame_name VARCHAR(60) NOT NULL,
    PRIMARY KEY (frame_id)
);

CREATE TABLE areas (
    area_id VARCHAR(20) NOT NULL,
    area_name VARCHAR(60) NOT NULL,
    PRIMARY KEY (area_id)
);


CREATE TABLE users (
    user_rpe VARCHAR(20) NOT NULL,
    user_mail VARCHAR(100) UNIQUE NOT NULL,
    user_role VARCHAR(30) NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    user_area VARCHAR(20) NOT NULL,
    cv_id BIGINT,
    situation VARCHAR(20),
    PRIMARY KEY (user_rpe),
    FOREIGN KEY (user_area) REFERENCES areas(area_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
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
    institution VARCHAR(30),
    degree_obtained VARCHAR(1),
    obtained_year INT,
    professional_license VARCHAR(10),
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
    level_participation INT,
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
    description VARCHAR(1200),
    PRIMARY KEY (contribution_id),
    FOREIGN KEY (cv_id) REFERENCES cvs(cv_id)
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
    reviser_id BIGINT NOT NULL, 
    description VARCHAR(255),
    seen BOOL NOT NULL,
    pinned BOOL NOT NULL DEFAULT FALSE,
    starred BOOL NOT NULL DEFAULT FALSE,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (user_rpe) REFERENCES users(user_rpe),
    FOREIGN KEY (evidence_id) REFERENCES evidences(evidence_id),
    FOREIGN KEY (reviser_id) REFERENCES revisers(reviser_id)
);

-- Insertar en la tabla roles
-- Nota: Los IDs de los roles deben coincidir con los usados en la tabla users
INSERT INTO role(role_id, role_name) VALUES
(1, 'ADMINISTRADOR'),
(2, 'JEFE DE AREA'),
(3, 'COORDINADOR DE CARRERA'),
(4, 'PROFESOR'),
(5, 'DIRECTIVO'),
(6, 'DEPARTAMENTO UNIVERSITARIO'),
(7, 'PERSONAL DE APOYO');

-- Insertar en la tabla permissions
-- Nota: Los IDs de los permisos deben coincidir con los usados en la tabla role_permissions
INSERT INTO permissions(permission_id, permission_name) VALUES
(1, 'Subir archivos'),
(2, 'Actualizar archivos'),
(3, 'Descargar archivos'),
(4, 'Eliminar archivos');

-- Insertar en la tabla role_permissions
-- Nota: Los IDs de los roles y permisos deben coincidir con los usados en las tablas roles y permissions

INSERT INTO role_permissions (role_id, permission_id, is_enabled) VALUES
(1, 1, true), (1, 2, true), (1, 3, true), (1, 4, true),
(2, 1, true), (2, 2, true), (2, 3, true), (2, 4, true),
(3, 1, true), (3, 2, true), (3, 3, true), (3, 4, true),
(4, 1, true), (4, 2, true), (4, 3, true), (4, 4, true),
(5, 1, false), (5, 2, false), (5, 3, true), (5, 4, false),
(6, 1, true), (6, 2, true), (6, 3, true), (6, 4, true),
(7, 1, true), (7, 2, true), (7, 3, true), (7, 4, true);

-- Insertar en la tabla cvs para Usuarios

INSERT INTO cvs (professor_number, update_date, professor_name, age, birth_date, actual_position, duration)
VALUES 
(10285, '2025-03-15', 'RAMOS BLANCO ALBERTO', 40, '1985-03-15', 'PROFESOR', 15),
(10314, '2025-04-15', 'MARTÍNEZ PÉREZ FRANCISCO EDUARDO', 38, '1985-03-15', 'COORDINADOR DE CARRERA', 20),
(18220, '2025-05-15', 'REYES CARDENAZ OSCAR', 42, '1985-03-15', 'PROFESOR', 20),
(3045, '2025-06-15', 'DIAZ QUIÑONES LILIA DEL CARMEN', 50, '1985-03-15', 'ADMINISTRADOR', 20);

-- Insertar en la tabla areas

INSERT INTO areas (area_id, area_name) VALUES
('AR01', 'Área Agroindustrial'),
('2', 'Área de Ciencias de la Computación'),
('AR03', 'Área de Ciencias de la Tierra'),
('3', 'Área Civil'),
('AR05', 'Área Mecánica y Eléctrica'),
('AR06', 'Área de Metalurgia y Materiales');

-- Insertar en la tabla careers
INSERT INTO careers (career_id, area_id, career_name) VALUES
('CA01', 'AR01', 'Ingeniería Agroindustrial'),
('CA02', 'AR03', 'Ingeniería Ambiental'),
('CA03', '3', 'Ingeniería Civil'),
('CA04', '2', 'Ingeniería en Computación'),
('CA05', 'AR05', 'Ingeniería en Electricidad y Automatización'),
('CA06', 'AR03', 'Ingeniería en Geología'),
('CA07', '2', 'Ingeniería en Sistemas Inteligentes'),
('CA08', '3', 'Ingeniería en Topografía y Construcción'),
('CA09', 'AR05', 'Ingeniería Mecánica'),
('CA10', 'AR05', 'Ingeniería Mecánica Administrativa'),
('CA11', 'AR05', 'Ingeniería Mecánica Eléctrica'),
('CA12', 'AR05', 'Ingeniería Mecatrónica'),
('CA13', 'AR06', 'Ingeniería Metalúrgica y de Materiales'),
('CA14', 'AR03', 'Ingeniería Geoinformática');


-- Insertar en la tabla users 
--***********************************************
-- Nota: esto esta porque la parte de situacion y el identificador al entrar por primeravez no 
--       se llena
--***********************************************
INSERT INTO users (user_rpe, user_mail, user_role, user_name, user_area, cv_id, situation)
VALUES ('10285', 'beto@uaslp.mx', 'PROFESOR', 'RAMOS BLANCO ALBERTO', 2, 1, 'Activo'),
       ('10314', 'eduardo.perez@uaslp.mx', 'COORDINADOR DE CARRERA', 'MARTÍNEZ PÉREZ FRANCISCO EDUARDO', 2, 2, 'Activo'),
       ('18220', 'oscar.reyes@uaslp.mx', 'PROFESOR', 'REYES CARDENAZ OSCAR', 2, 3, 'Activo'),
       ('3045', 'diaquili@uaslp.mx', 'ADMINISTRADOR', 'DIAZ QUIÑONES LILIA DEL CARMEN', 2, 4, 'Activo');



-- Insertar en la tabla frames_of_reference NOTA: este el maximo el nombre ofcial lleva: de la ingenieria 
INSERT INTO frames_of_reference (frame_id, frame_name)
VALUES (1, 'Marco de Referencia 2025 para la acreditación de programas'),
       (2, 'Marco de Referencia 2026 para la acreditación de programas'),
       (3, 'Marco de Referencia 2027 para la acreditación de programas'),
       (4, 'Marco de Referencia 2028 para la acreditación de programas'),
       (5, 'Marco de Referencia 2029 para la acreditación de programas'),
       (6, 'Marco de Referencia 2030 para la acreditación de programas');


-- Insertar en la tabla categories 
--***********************************************
-- Nota: fALTA PONER DESCRIPCIÓN DE CATEGORÍA
--***********************************************

INSERT INTO categories (category_id, category_name, frame_id, indice)
VALUES (1, 'Categoría 1. Estudiantes', 1, 1),
       (2, 'Categoría 2. Plan de Estudios', 1, 2),
       (3, 'Categoría 3. Objetivos Educacionales', 1, 3),
       (4, 'Categoría 4. Atributos de Egreso', 1, 4),
       (5, 'Categoría 5. Personal Académico', 1, 5),
       (6, 'Categoría 6. Soporte Institucional', 1, 6),
       (7, 'Categoría 7. Mejora Continua', 1, 7),
       (8, 'Categoría 8. Áreas de Especialidad de los Programas', 1, 8);

-- Insertar en la tabla sections NOTA: LA descripciones en el documento son demasiadas largas 2000 caracteres
-- por lo que se recomienda hacer un resumen de las mismas para que no haya problemas al insertar o no se si es enfocado a evidencias

INSERT INTO sections (section_id, category_id, section_name, section_description, indice, is_Standard)
VALUES (1, 1, 'Admisión', 'El PE asegura un proceso transparente de atracción y admisión, alineado a la responsabilidad social, perfil de ingreso y estudios previos.', 1, TRUE),
        (2, 1, 'Revalidación, equivalencia y reconocimiento de otros estudios', 'El PE aplica un proceso transparente de admisión, acorde al perfil de ingreso y estudios previos de los aspirantes', 2, TRUE),
        (3, 1, 'Privacidad de los datos del estudiante', 'La IES protege la privacidad de los datos de los estudiantes conforme a la Ley Federal de Protección de Datos Personales', 3, TRUE),
        (4, 1, 'Integridad académica', 'La IES asegura integridad académica con normatividad, programas éticos y verificación de identidad en educación a distancia.', 4, TRUE),
        (5, 1, 'Trayectoria escolar', 'El PE da seguimiento al desempeño por cohorte, detecta áreas de mejora y protege la confidencialidad de la información estudiantil.', 5, TRUE),
        (6, 1, 'Asesoría y tutoría', 'El PE ofrece tutorías, asesorías y programas de apoyo que favorecen la retención, eficiencia terminal y formación integral del estudiante.', 6, TRUE),
        (7, 1, 'Titulación', 'El PE aplica políticas y procesos transparentes para egreso y titulación, garantizando requisitos como el servicio social.', 7, TRUE),
        (8, 1, 'Comportamientos apropiados', 'La IES promueve conductas apropiadas con normativa que garantiza equidad social, de género e inclusión en la comunidad educativa.', 8, TRUE),

---Categoría 2. Plan de Estudios

        (9, 2, 'Organización curricular', 'El PDE del PE cumple los requerimientos del CACEI, considerando los ejes curriculares y sus características específicas', 1, FALSE),
        (10, 2, 'Problemas de ingeniería complejos', 'El PE prepara a los estudiantes para resolver problemas de ingeniería complejos usando matemáticas, ciencias y conocimientos aplicados', 2, TRUE),
        (11, 2, 'Experiencia en diseño', 'El PE ofrece experiencia de diseño relevante, integrando conocimientos previos, normas de ingeniería y múltiples restricciones.', 3, TRUE),
        (12, 2, 'Flexibilidad curricular', 'El PE aplica al menos tres estrategias que lo hacen flexible y alineado a las necesidades formativas y atributos de egreso.', 4, TRUE),

---Categoría 3. Objetivos Educacionales

        (13, 3, 'Definición y difusión de los objetivos educacionales del programa educativo', 'Los objetivos educacionales (OE) del PE deben ser públicos, conocidos y accesibles para toda la comunidad interna y externa.', 1, FALSE),
        (14, 3, 'Valoración del logro de los objetivos educacionales del programa educativo', 'Debe haber un proceso periódico para medir y documentar el logro de los objetivos educacionales del programa educativo.', 2, FALSE),

---Categoría 4. Atributos de Egreso

        (15, 4, 'Definición y difusión de los atributos de egreso del programa educativo', 'Los atributos de egreso (AE) del PE deben ser públicos, conocidos y accesibles para toda la comunidad interna y externa.', 1, FALSE),
        (16, 4, 'Valoración del logro de los atributos de egreso del programa educativo', 'Debe haber un proceso periódico para medir y documentar el logro de los atributos de egreso del programa educativo.', 2, FALSE),
        (17, 4, 'Logro de los atributos de egreso', 'El PE documenta los resultados de los atributos de egreso en cada ciclo para evaluar el cumplimiento del perfil y mejorar el programa.', 3, FALSE),

---Categoría 5. Personal Académico

        (18, 5, 'Perfil del personal académico', 'El personal académico del PE posee competencias adecuadas para su desarrollo, considerando factores clave para su desempeño.', 1, FALSE),
        (19, 5, 'Suficiencia del personal académico', 'El PE cuenta con personal académico suficiente y competente en lo académico, profesional y didáctico para cubrir todas las áreas.', 2, TRUE),
        (20, 5, 'Distribución de actividades sustantivas', 'Las actividades académicas del PE se distribuyen adecuadamente entre los profesores responsables y el núcleo básico del programa.', 3, TRUE),
        (21, 5, 'Evaluación y desarrollo de personal académico', 'El PE cuenta con un sistema integral de evaluación académica, incluyendo estudiantes, pares y autoridades, para desarrollo docente.', 4, TRUE),
        (22, 5, 'Autoridad y responsabilidad del personal académico del programa educativo', 'El PE cuenta con procesos documentados donde instancias académicas revisan cursos, AE y OE, usando los resultados para mejora continua.', 5, TRUE),
        (23, 5, 'Selección, permanencia y retención del personal académico', 'El PE tiene un proceso transparente para selección y permanencia de profesores, considerando formación, experiencia y retención de desempeño.', 6, TRUE),
       
---Categoría 6. Soporte Institucional

        
        (24, 6, 'Infraestructura y equipamiento', 'La IES dispone de infraestructura, equipos, manuales y personal adecuado para atender las necesidades del PE y servicios académicos.', 1, FALSE),
        (25, 6, 'Liderazgo institucional', 'El PE cuenta con estructura, normatividad y liderazgo institucional que garantizan políticas claras, planeación y mejora continua.', 2, TRUE),
        (26, 6, 'Recursos financieros', 'El PE dispone de recursos financieros suficientes para contratar, retener y desarrollar personal, así como mantener infraestructura y equipos.', 3, TRUE),

---Categoría 7. Mejora Continua

        (27, 7, 'Definición y justificación de los grupos de interés del programa educativo', 'El PE define y justifica sus grupos de interés y representantes, considerando propuestas relevantes que los egresados puedan atender.', 1, TRUE),
        (28, 7, 'Proceso de mejora', 'El PE valida, analiza y utiliza sistemáticamente sus indicadores para evaluar y mejorar el programa durante todo el ciclo del estudiante.', 2, FALSE),
        

---Categoría 8. Áreas de Especialidad de los Programas

        (29, 8, 'Programas denominados Ingeniería Aeronáutica, Aeroespacial o similares', 'El PE utiliza los resultados de sus procesos de evaluación para tomar decisiones informadas que conduzcan a su mejora continua.', 1, TRUE),
        (30, 8, 'Programas denominados Ingeniería Agrícola, Forestal o similares.', 'El programa prepara egresados con sólidos conocimientos en ciencias básicas e ingeniería, incluyendo matemáticas y biología, aplicables a su campo', 2, TRUE),
        (31, 8, 'Programas denominados Ingeniería de Alimentos o similares', 'El programa garantiza egresados con competencias en matemáticas, ciencias, ingeniería de alimentos y habilidades en diseño y aplicación de sistemas.', 3, TRUE),
        (32, 8, 'Programas denominados Ingeniería Ambiental o similares.', 'El PE forma egresados en ingeniería ambiental y de sustentabilidad, con sólidos conocimientos, habilidades de laboratorio, diseño de sistemas, análisis de impacto y normativa ambiental.', 4, TRUE),
        (33, 8, 'Programas denominados Ingeniería Biomédica o similares', 'El PE en ingeniería biomédica forma egresados con sólidos conocimientos en ciencias e ingeniería, capaces de resolver problemas complejos y diseñar sistemas considerando impactos sociales, económicos y ambientales.', 5, TRUE),
        (34, 8, 'Programas denominados Ingeniería en Biotecnología, Ingeniería Bioquímica o similares', 'El PE en biotecnología forma egresados con sólidos conocimientos en ingeniería y ciencias, capaces de resolver problemas complejos y diseñar sistemas considerando impactos sociales, económicos y ambientales.', 6, TRUE),
        (35, 8, 'Programas denominados Ingeniería en Ciberseguridad, Seguridad Computacional o similares', 'El PE en ciberseguridad forma egresados con sólidos conocimientos en matemáticas, ingeniería e informática para diseñar, proteger y evaluar sistemas complejos.', 7, TRUE),
        (36, 8, 'Programas denominados Ingeniería Civil, Ingeniería en Construcción o similares', 'El PE en ingeniería civil forma egresados con sólidos conocimientos, competencias de diseño y gestión, capaces de resolver problemas complejos considerando impactos sociales, económicos y ambientales', 8, TRUE),
        (37, 8, 'Programas denominados Ingeniería en Ciencias Computacionales, Ingeniería en Computación o similares', 'El PE en ingeniería eléctrica y computación forma egresados con sólidos conocimientos en ciencias, ingeniería y software para diseñar sistemas considerando impactos sociales, económicos y ambientales.', 9, TRUE),
        (38, 8, 'Programas denominados Ingeniería Eléctrica, Ingeniería Electrónica, Ingeniería en Telecomunicaciones o similares', 'El PE en eléctrica, electrónica y telecomunicaciones forma egresados capaces de diseñar sistemas complejos considerando impactos y seguridad.', 10, TRUE),
        (39, 8, 'Programas denominados Ingeniería Física, Ciencias de la Ingeniería o similares', 'El PE en física e ingeniería forma egresados con sólidos conocimientos en matemáticas, física y áreas aplicadas para resolver problemas complejos', 11, TRUE),
        (40, 8, 'Programas denominados Ingeniería en Fotometría, Óptica o similares', 'El PE en óptica y fotónica forma egresados con sólidos conocimientos en ingeniería y ciencias básicas para diseñar y analizar dispositivos ópticos.', 12, TRUE),
        (41, 8, 'Programas denominados Ingeniería Geológica o similares', 'El PE en ingeniería geológica forma egresados capaces de aplicar matemáticas, ciencias e ingeniería para resolver problemas geológicos complejos', 13, TRUE),
        (42, 8, 'Programas denominados Ingeniería en Gestión Empresarial o similares', 'El PE en ingeniería en gestión forma egresados capaces de diseñar y mejorar sistemas integrados, combinando ingeniería y gestión organizacional.', 14, TRUE),
        (43, 8, 'Programas denominados Ingeniería Industrial, Ingeniería en Producción o similares.', 'El PE en ingeniería industrial forma egresados capaces de diseñar y mejorar sistemas integrados, optimizando manufactura y recursos.', 15, TRUE),
        (44, 8, 'Programas denominados Ingeniería en Manufactura o similares', 'El PE en manufactura forma egresados competentes en diseño de procesos, productos, sistemas, competitividad y análisis de manufactura.', 16, TRUE),
        (45, 8, 'Programas denominados Ingeniería Mecánica o similares', 'El PE en ingeniería térmica y mecánica forma egresados capaces de aplicar ciencias, matemáticas e ingeniería para diseñar y analizar sistemas.', 17, TRUE),
        (46, 8, 'Programas denominados Ingeniería Mecatrónica o similares', 'El PE forma egresados con sólidos conocimientos en física, matemáticas, electrónica y sistemas electromecánicos para diseñar y analizar.', 18, TRUE),
        (47, 8, 'Programas denominados Ingeniería Metalúrgica, Ingeniería de Materiales o similares', 'El PE en ciencia de materiales forma egresados con sólidos conocimientos en ciencias, ingeniería y computación para analizar y diseñar materiales.', 19, TRUE),
        (48, 8, 'Programas denominados Ingeniería en Minas o similares', 'El PE en ingeniería de minas forma egresados competentes en matemáticas, ciencias, geología e ingeniería para resolver problemas mineros.', 20, TRUE),
        (49, 8, 'Programas denominados Ingeniería Naval, Ingeniería en Arquitectura Naval, Ingeniería Marina o similares.', 'El PE en arquitectura naval forma egresados con sólidos conocimientos en matemáticas, mecánica, fluidos y sistemas marinos para ingeniería marina.', 21, TRUE),
        (50, 8, 'Programas denominados Ingeniería Nuclear o similares', 'El PE en ingeniería nuclear forma egresados capaces de aplicar matemáticas, ciencias e ingeniería para diseñar y medir sistemas nucleares', 22, TRUE),
        (51, 8, 'Programas denominados Ingeniería Oceánica o similares', 'El PE forma egresados que aplican mecánica, oceanografía y acústica submarina al diseño sistémico y optimización en ingeniería', 23, TRUE),
        (52, 8, 'Programas denominados Ingeniería Petrolera o similares', 'El PE forma egresados en matemáticas, mecánica, fluidos y yacimientos para optimizar recursos con diseño, gestión y economía.', 24, TRUE),
        (53, 8, 'Programas denominados Ingeniería Química o similares', 'El PE forma egresados en química, procesos, control y diseño de plantas químicas, con prácticas en flujo, calor, separación y reacciones.', 25, TRUE),
        (54, 8, 'Programas denominados Ingeniería en Topografía, Geomática o similares.', 'El PE prepara egresados competentes en topografía, SIG, fotogrametría, mapeo, geodesia, sensores remotos y áreas afines.', 26, TRUE);

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
INSERT INTO professional_achievements (achievement_id, cv_id, description)
VALUES (1, 1, 'Desarrollador principal de un sistema de gestión universitaria');

-- Insertar en la tabla participations
INSERT INTO participations (participation_id, cv_id, institution, period, level_participation)
VALUES (1, 1, 'UASLP', 2, 'Nacional');

-- Insertar en la tabla awards
INSERT INTO awards (award_id, cv_id, description)
VALUES (1, 1, 'Premio a la Innovación en Tecnología Educativa');

-- Insertar en la tabla contributions_to_pe
INSERT INTO contributions_to_pe (contribution_id, cv_id, description)
VALUES (1, 1, 'Contribución al diseño de plataformas de educación en línea');

-- Insertar en la tabla subjects
INSERT INTO subjects (subject_id, subject_name, career_id)
VALUES (1, 'Programación Avanzada', 'C01');

-- Insertar en la tabla groups
INSERT INTO groups (group_id, semester, type_a, period_a, subject_id, hour_a)
VALUES (1, '2025-1', TRUE, 'Mañana', 1, '09:00');

-- Insertar en la tabla accreditation_processes
INSERT INTO accreditation_processes (process_id, career_id, frame_id, process_name, start_date, end_date, due_date)
VALUES (1, 'C01', 1, 'Acreditación de Ingeniería en Sistemas', '2025-01-01', '2025-12-31', '2025-12-01');

-- Insertar en la tabla statuses
INSERT INTO statuses (status_id, status_description, user_rpe, evidence_id, status_date, feedback)
VALUES (1, 'APROBADO', '10285', 1, '2025-04-22', 'Muy bueno'),
(2, 'NO APROBADO', '10285', 2, '2025-04-22', 'Malo'),
(3, 'PENDIENTE', '10285', 3, '2025-04-22', null);


-- Insertar en la tabla files
INSERT INTO files (file_id, file_url, upload_date, evidence_id, justification, file_name)
VALUES (1, 'http://files.uaslp.edu.mx/evidences/1.pdf', '2025-04-22', 1, 'Justificación de la evidencia', 'Evidencia_1.pdf');

-- Insertar en la tabla notifications
INSERT INTO notifications (notification_id, title, evidence_id, notification_date, user_rpe, reviser_id, description, seen, pinned, starred)
VALUES (1, 'Nueva Evidencia para Revisión', 1, '2025-04-22', '10285', 1, 'Revisión pendiente de evidencia', FALSE, FALSE, FALSE);




