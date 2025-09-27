
-- Insertar en la tabla roles
-- Nota: Los IDs de los roles deben coincidir con los usados en la tabla users
INSERT INTO role(role_id, role_name) VALUES
(1, 'ADMINISTRADOR'),
(2, 'JEFE DE AREA'),
(3, 'COORDINADOR'),
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
(10285, '2025-03-15', 'RAMOS BLANCO ALBERTO', 40, '1985-03-15', 'Profesor', 15),
(10314, '2025-04-15', 'MARTÍNEZ PÉREZ FRANCISCO EDUARDO', 38, '1985-03-15', 'Coordinador de Carrera', 20),
(18220, '2025-05-15', 'REYES CARDENAZ OSCAR', 42, '1985-03-15', 'Profesor', 20),
(3045, '2025-06-15', 'DIAZ QUIÑONES LILIA DEL CARMEN', 50, '1985-03-15', 'Administrador', 20);

-- Insertar en la tabla areas

INSERT INTO areas (area_id, area_name) VALUES
('0', 'Departamento FI-MA'),
('1', 'Área Humanistica'),
('2', 'Área de Ciencias de la Computación'),
('3', 'Área Civil'),
('4', 'Área de Ciencias de la Tierra'),
('5', 'Area de Mecanica Electrica'),
('6', 'Área de Metalurgia y Materiales'),
('7', 'Area de Agroindustrial'),
('8', 'DUI');

-- Insertar en la tabla careers
INSERT INTO careers (career_id, area_id, career_name) VALUES
('CA01', '7', 'Ingeniería Agroindustrial'),                  -- Área Agroindustrial
('CA02', '4', 'Ingeniería Ambiental'),                       -- Ciencias de la Tierra
('CA03', '3', 'Ingeniería Civil'),                           -- Civil
('CA04', '2', 'Ingeniería en Computación'),                  -- Computación
('CA05', '5', 'Ingeniería en Electricidad y Automatización'),-- Mecánica Eléctrica
('CA06', '4', 'Ingeniería en Geología'),                     -- Ciencias de la Tierra
('CA07', '2', 'Ingeniería en Sistemas Inteligentes'),        -- Computación
('CA08', '3', 'Ingeniería en Topografía y Construcción'),    -- Civil
('CA09', '5', 'Ingeniería Mecánica'),                        -- Mecánica Eléctrica
('CA10', '5', 'Ingeniería Mecánica Administrativa'),         -- Mecánica Eléctrica
('CA11', '5', 'Ingeniería Mecánica Eléctrica'),              -- Mecánica Eléctrica
('CA12', '5', 'Ingeniería Mecatrónica'),                     -- Mecánica Eléctrica
('CA13', '6', 'Ingeniería Metalúrgica y de Materiales'),     -- Metalurgia y Materiales
('CA14', '4', 'Ingeniería Geoinformática');                  -- Ciencias de la Tierra



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

INSERT INTO categories (category_id, category_name, frame_id, indice)
VALUES 
(1, 'Categoría 1. Estudiantes', 1, 1),
(2, 'Categoría 2. Plan de Estudios', 1, 2),
(3, 'Categoría 3. Objetivos Educacionales', 1, 3),
(4, 'Categoría 4. Atributos de Egreso', 1, 4),
(5, 'Categoría 5. Personal Académico', 1, 5),
(6, 'Categoría 6. Soporte Institucional', 1, 6),
(7, 'Categoría 7. Mejora Continua', 1, 7),
(8, 'Categoría 8. Áreas de Especialidad de los Programas', 1, 8);

INSERT INTO sections (section_id, category_id, section_name, section_description, indice, is_standard)
VALUES 
-- Categoría 1
(1, 1, 'Admisión', 'Proceso transparente de atracción y admisión conforme a perfil y estudios previos.', 1, TRUE),
(2, 1, 'Revalidación', 'Proceso transparente de equivalencia y reconocimiento de estudios previos.', 2, TRUE),
(3, 1, 'Privacidad de datos', 'La IES protege la privacidad de datos conforme a la Ley de Protección de Datos.', 3, TRUE),
(4, 1, 'Integridad académica', 'Normatividad y programas éticos que aseguran integridad académica.', 4, TRUE),
(5, 1, 'Trayectoria escolar', 'Seguimiento a desempeño por cohorte y mejora de resultados académicos.', 5, TRUE),
(6, 1, 'Asesoría y tutoría', 'Tutorías y programas de apoyo que favorecen retención y formación integral.', 6, TRUE),
(7, 1, 'Titulación', 'Políticas y procesos claros para egreso y titulación, incluyendo requisitos.', 7, TRUE),
(8, 1, 'Conductas apropiadas', 'La IES promueve conductas con equidad, inclusión y normativas claras.', 8, TRUE),

-- Categoría 2
(9, 2, 'Organización curricular', 'El plan de estudios cumple requisitos del CACEI y ejes curriculares.', 1, FALSE),
(10, 2, 'Problemas complejos', 'Formación para resolver problemas de ingeniería con base científica.', 2, TRUE),
(11, 2, 'Experiencia en diseño', 'Integra conocimientos, normas y restricciones en proyectos de diseño.', 3, TRUE),
(12, 2, 'Flexibilidad curricular', 'El PE aplica estrategias que lo hacen flexible y alineado a egreso.', 4, TRUE),

-- Categoría 3
(13, 3, 'Difusión de OE', 'Los objetivos educacionales del PE son públicos y accesibles.', 1, FALSE),
(14, 3, 'Valoración de OE', 'Proceso periódico para medir y documentar logro de objetivos.', 2, FALSE),

-- Categoría 4
(15, 4, 'Difusión de AE', 'Los atributos de egreso del PE son públicos y accesibles.', 1, FALSE),
(16, 4, 'Valoración de AE', 'Proceso periódico para medir y documentar logro de atributos.', 2, FALSE),
(17, 4, 'Logro de AE', 'Resultados documentados de atributos de egreso por ciclo.', 3, FALSE),

-- Categoría 5
(18, 5, 'Perfil académico', 'El personal académico cuenta con competencias adecuadas.', 1, FALSE),
(19, 5, 'Suficiencia', 'El PE tiene personal suficiente y competente en docencia.', 2, TRUE),
(20, 5, 'Distribución de actividades', 'Actividades académicas distribuidas entre profesores.', 3, TRUE),
(21, 5, 'Evaluación docente', 'Sistema integral de evaluación académica y desarrollo.', 4, TRUE),
(22, 5, 'Autoridad y responsabilidad', 'Procesos documentados para revisión y mejora de cursos.', 5, TRUE),
(23, 5, 'Selección y retención', 'Proceso transparente para selección y permanencia docente.', 6, TRUE),

-- Categoría 6
(24, 6, 'Infraestructura', 'La IES cuenta con infraestructura, equipos y personal adecuado.', 1, FALSE),
(25, 6, 'Liderazgo', 'Estructura y liderazgo institucional que asegura planeación.', 2, TRUE),
(26, 6, 'Recursos financieros', 'Dispone de recursos suficientes para personal e infraestructura.', 3, TRUE),

-- Categoría 7
(27, 7, 'Grupos de interés', 'Definición y justificación de grupos de interés del PE.', 1, TRUE),
(28, 7, 'Proceso de mejora', 'Uso de indicadores para evaluar y mejorar el programa.', 2, FALSE),

-- Categoría 8
(29, 8, 'Ing. Aeronáutica', 'Decisiones basadas en procesos de evaluación continua.', 1, TRUE),
(30, 8, 'Ing. Agrícola', 'Egresados con conocimientos en ciencias básicas y biología.', 2, TRUE),
(31, 8, 'Ing. Alimentos', 'Egresados con competencias en matemáticas e ingeniería.', 3, TRUE),
(32, 8, 'Ing. Ambiental', 'Formación en ingeniería ambiental y sustentabilidad.', 4, TRUE),
(33, 8, 'Ing. Biomédica', 'Egresados con sólidos conocimientos en ciencias aplicadas.', 5, TRUE),
(34, 8, 'Ing. Biotecnología', 'Egresados capaces de resolver problemas complejos.', 6, TRUE),
(35, 8, 'Ing. Ciberseguridad', 'Egresados en diseño y protección de sistemas complejos.', 7, TRUE),
(36, 8, 'Ing. Civil', 'Egresados con competencias de diseño y gestión civil.', 8, TRUE),
(37, 8, 'Ing. Computación', 'Egresados en ingeniería eléctrica y software avanzado.', 9, TRUE),
(38, 8, 'Ing. Eléctrica', 'Formación en diseño de sistemas eléctricos y seguros.', 10, TRUE),
(39, 8, 'Ing. Física', 'Egresados con bases en matemáticas y física aplicada.', 11, TRUE),
(40, 8, 'Ing. Óptica', 'Egresados en diseño y análisis de sistemas ópticos.', 12, TRUE),
(41, 8, 'Ing. Geológica', 'Aplicación de matemáticas y ciencias a problemas geológicos.', 13, TRUE),
(42, 8, 'Ing. Gestión', 'Egresados capaces de diseñar y mejorar sistemas integrados.', 14, TRUE),
(43, 8, 'Ing. Industrial', 'Egresados que optimizan manufactura y recursos.', 15, TRUE),
(44, 8, 'Ing. Manufactura', 'Competencias en diseño de procesos y productos.', 16, TRUE),
(45, 8, 'Ing. Mecánica', 'Formación en diseño y análisis de sistemas mecánicos.', 17, TRUE),
(46, 8, 'Ing. Mecatrónica', 'Formación en electrónica y sistemas electromecánicos.', 18, TRUE),
(47, 8, 'Ing. Materiales', 'Egresados en análisis y diseño de materiales.', 19, TRUE),
(48, 8, 'Ing. Minas', 'Egresados competentes en problemas mineros.', 20, TRUE),
(49, 8, 'Ing. Naval', 'Formación en matemáticas, mecánica y sistemas marinos.', 21, TRUE),
(50, 8, 'Ing. Nuclear', 'Formación para diseñar y medir sistemas nucleares.', 22, TRUE),
(51, 8, 'Ing. Oceánica', 'Egresados aplican mecánica y oceanografía.', 23, TRUE),
(52, 8, 'Ing. Petrolera', 'Egresados en mecánica y fluidos aplicados a recursos.', 24, TRUE),
(53, 8, 'Ing. Química', 'Formación en química, procesos y plantas químicas.', 25, TRUE),
(54, 8, 'Ing. Topografía', 'Egresados en SIG, fotogrametría y sensores remotos.', 26, TRUE);


-- ===================== EDUCATIONS =====================
INSERT INTO educations (cv_id, institution, degree_obtained, obtained_year, professional_license, degree_name)
VALUES
(1, 'UASLP', 'L', 2007, '1234567', 'Licenciatura en Matemáticas'),
(1, 'UNAM', 'M', 2010, '7654321', 'Maestría en Educación'),
(1, 'ITESM', 'D', 2015, '4567890', 'Doctorado en Ingeniería'),
(1, 'UASLP', 'C', 2005, '1112223', 'Curso en Docencia Universitaria'),

(2, 'UASLP', 'L', 2008, '2345678', 'Licenciatura en Sistemas'),
(2, 'UAM',  'M', 2011, '8765432', 'Maestría en Informática'),
(2, 'IPN',  'D', 2016, '5678901', 'Doctorado en Ciencias Computacionales'),
(2, 'UASLP', 'C', 2006, '1122334', 'Diplomado en Gestión Académica'),

(3, 'UASLP', 'L', 2006, '3456789', 'Licenciatura en Física'),
(3, 'UAEM', 'M', 2009, '9876543', 'Maestría en Materiales'),
(3, 'ITESM', 'D', 2014, '6789012', 'Doctorado en Energías Renovables'),
(3, 'UASLP', 'C', 2004, '2233445', 'Diplomado en Docencia'),

(4, 'UASLP', 'L', 2000, '4567891', 'Licenciatura en Administración'),
(4, 'UDEM', 'M', 2003, '1987654', 'Maestría en Gestión Educativa'),
(4, 'IPN',  'D', 2008, '7890123', 'Doctorado en Ciencias Administrativas'),
(4, 'UASLP', 'C', 1999, '3344556', 'Curso de Liderazgo Educativo');

-- ===================== TEACHER_TRAININGS =====================
INSERT INTO teacher_trainings (title_certification, obtained_year, institution_country, hours, cv_id)
VALUES
('Didáctica Avanzada', 2018, 'México', 40, 1),
('TIC en Educación', 2019, 'México', 30, 1),
('Evaluación Educativa', 2020, 'México', 25, 1),
('Competencias Docentes', 2021, 'México', 50, 1),

('Liderazgo Académico', 2017, 'México', 40, 2),
('Gestión Escolar', 2018, 'México', 35, 2),
('Innovación en Enseñanza', 2020, 'México', 30, 2),
('Calidad Educativa', 2021, 'México', 45, 2),

('Metodología STEM', 2016, 'México', 30, 3),
('Docencia en Ciencias', 2017, 'México', 25, 3),
('Estrategias Pedagógicas', 2019, 'México', 40, 3),
('Innovación Docente', 2021, 'México', 50, 3),

('Administración Académica', 2015, 'México', 40, 4),
('Liderazgo Educativo', 2016, 'México', 30, 4),
('Normatividad Escolar', 2018, 'México', 35, 4),
('Gestión de Calidad', 2020, 'México', 45, 4);

-- ===================== DISCIPLINARY_UPDATES =====================
INSERT INTO disciplinary_updates (cv_id, title_certification, year_certification, institution_country, hours)
VALUES
(1, 'Actualización Matemáticas', 2018, 'México', 20),
(1, 'Seminario Álgebra', 2019, 'México', 25),
(1, 'Congreso Educación', 2020, 'México', 30),
(1, 'Taller Innovación', 2021, 'México', 15),

(2, 'Actualización TIC', 2017, 'México', 20),
(2, 'Seminario Redes', 2018, 'México', 25),
(2, 'Congreso Informática', 2019, 'México', 30),
(2, 'Taller Ciberseguridad', 2021, 'México', 15),

(3, 'Actualización Física', 2016, 'México', 20),
(3, 'Seminario Materiales', 2017, 'México', 25),
(3, 'Congreso Energía', 2019, 'México', 30),
(3, 'Taller Renovables', 2020, 'México', 15),

(4, 'Actualización Administración', 2015, 'México', 20),
(4, 'Seminario Gestión', 2016, 'México', 25),
(4, 'Congreso Educación', 2018, 'México', 30),
(4, 'Taller Liderazgo', 2020, 'México', 15);

-- ===================== ACADEMIC_MANAGEMENTS =====================
INSERT INTO academic_managements (cv_id, job_position, institution, start_date, end_date)
VALUES
(1, 'Jefe de Departamento', 'UASLP', '2015-01', '2017-12'),
(1, 'Coordinador Académico', 'UASLP', '2018-01', '2020-12'),
(1, 'Secretario Académico', 'UASLP', '2021-01', '2022-12'),
(1, 'Director de Área', 'UASLP', '2023-01', '2024-12'),

(2, 'Coordinador de Carrera', 'UASLP', '2016-01', '2018-12'),
(2, 'Subdirector', 'UASLP', '2019-01', '2020-12'),
(2, 'Director', 'UASLP', '2021-01', '2022-12'),
(2, 'Consejero Académico', 'UASLP', '2023-01', '2024-12'),

(3, 'Responsable de Laboratorio', 'UASLP', '2014-01', '2016-12'),
(3, 'Coordinador de Investigación', 'UASLP', '2017-01', '2019-12'),
(3, 'Secretario Técnico', 'UASLP', '2020-01', '2021-12'),
(3, 'Jefe de División', 'UASLP', '2022-01', '2023-12'),

(4, 'Administrador Académico', 'UASLP', '2010-01', '2012-12'),
(4, 'Jefa de Área', 'UASLP', '2013-01', '2015-12'),
(4, 'Directora de División', 'UASLP', '2016-01', '2018-12'),
(4, 'Consejera Académica', 'UASLP', '2019-01', '2021-12');

-- ===================== ACADEMIC_PRODUCTS =====================
INSERT INTO academic_products (cv_id, academic_product_number, description)
VALUES

(1, 1, 'Modelo matemático avanzado aplicado a enseñanza de álgebra y cálculo en educación superior.'),
(1, 2, 'Ponencia sobre innovación pedagógica en educación matemática y uso de tecnología en aula.'),
(1, 3, 'Publicación de libro de texto de álgebra y cálculo con ejercicios prácticos y aplicaciones.'),
(1, 4, 'Capítulo de libro sobre competencias digitales y aprendizaje colaborativo en matemáticas.'),


(2, 1, 'Artículo sobre inteligencia artificial aplicada a sistemas educativos, destacando algoritmos predictivos.'),
(2, 2, 'Ponencia sobre software educativo y plataformas de aprendizaje virtual con evaluación adaptativa.'),
(2, 3, 'Desarrollo de aplicación educativa para gestión de recursos académicos en programas de ingeniería.'),
(2, 4, 'Capítulo de libro sobre redes y seguridad informática en educación, con estrategias de implementación.'),


(3, 1, 'Artículo en física sobre experimentación avanzada en laboratorios universitarios y técnicas de medición.'),
(3, 2, 'Ponencia sobre materiales avanzados en congreso internacional de física aplicada e investigación docente.'),
(3, 3, 'Publicación de libro sobre energías renovables, con capítulos de estudio de casos y diseño de sistemas.'),
(3, 4, 'Capítulo de libro sobre nanotecnología en laboratorios de investigación y docencia universitaria.'),


(4, 1, 'Artículo sobre gestión administrativa educativa, planificación estratégica y control de recursos.'),
(4, 2, 'Ponencia en congreso nacional de gestión educativa sobre liderazgo académico y mejora de procesos.'),
(4, 3, 'Publicación de libro sobre administración educativa, con capítulos de planificación y coordinación.'),
(4, 4, 'Capítulo de libro sobre estrategias de innovación y liderazgo en administración académica.');

-- ===================== LABORAL_EXPERIENCES =====================
INSERT INTO laboral_experiences (cv_id, company_name, position, start_date, end_date)
VALUES
(1, 'Colegio San Luis', 'Profesor de matemáticas', '2005-01', '2007-12'),
(1, 'Instituto Tecnológico', 'Docente', '2008-01', '2010-12'),
(1, 'Preparatoria Estatal', 'Coordinador académico', '2011-01', '2013-12'),
(1, 'Universidad Autónoma', 'Profesor investigador', '2014-01', '2015-12'),

(2, 'Colegio de Informática', 'Docente', '2006-01', '2008-12'),
(2, 'Centro de Cómputo', 'Analista', '2009-01', '2010-12'),
(2, 'Instituto Politécnico', 'Profesor', '2011-01', '2013-12'),
(2, 'Universidad Autónoma', 'Coordinador de sistemas', '2014-01', '2015-12'),

(3, 'Colegio Nacional', 'Profesor de Física', '2004-01', '2006-12'),
(3, 'Laboratorio Nacional', 'Investigador', '2007-01', '2009-12'),
(3, 'Instituto Tecnológico', 'Profesor titular', '2010-01', '2012-12'),
(3, 'Universidad Estatal', 'Coordinador de laboratorio', '2013-01', '2014-12'),

(4, 'Colegio de Negocios', 'Docente', '1998-01', '2000-12'),
(4, 'Instituto de Gestión', 'Administradora', '2001-01', '2003-12'),
(4, 'Universidad Nacional', 'Directora de área', '2004-01', '2006-12'),
(4, 'Centro Académico', 'Consejera académica', '2007-01', '2008-12');

-- ===================== ENGINEERING_DESIGNS =====================
INSERT INTO engineering_designs (cv_id, institution, period, level_experience)
VALUES
(1, 'UASLP', 2010, 'Básico'),
(1, 'UASLP', 2012, 'Intermedio'),
(1, 'UASLP', 2015, 'Avanzado'),
(1, 'UASLP', 2018, 'Experto'),

(2, 'UASLP', 2011, 'Básico'),
(2, 'UASLP', 2013, 'Intermedio'),
(2, 'UASLP', 2016, 'Avanzado'),
(2, 'UASLP', 2019, 'Experto'),

(3, 'UASLP', 2009, 'Básico'),
(3, 'UASLP', 2011, 'Intermedio'),
(3, 'UASLP', 2014, 'Avanzado'),
(3, 'UASLP', 2017, 'Experto'),

(4, 'UASLP', 2000, 'Básico'),
(4, 'UASLP', 2003, 'Intermedio'),
(4, 'UASLP', 2006, 'Avanzado'),
(4, 'UASLP', 2009, 'Experto');

-- ===================== PROFESSIONAL_ACHIEVEMENTS =====================
INSERT INTO professional_achievements (achievement_id, cv_id, description)
VALUES

(1, 1, 'Reconocimiento al mérito académico por metodologías innovadoras en enseñanza de matemáticas, integración de herramientas digitales y estrategias colaborativas que mejoraron el aprendizaje y la eficiencia de los estudiantes.'),
(2, 1, 'Premio a la innovación docente por plan integral de formación estudiantil con evaluación formativa, seguimiento de cohorte y tutorías, aumentando retención y eficiencia terminal.'),
(3, 1, 'Coordinación de programas educativos con procesos transparentes de admisión, seguimiento académico y titulación, fomentando participación de docentes y estudiantes y garantizando la acreditación institucional.'),
(4, 1, 'Publicación de libro académico y capítulos especializados integrando prácticas de laboratorio, aplicaciones en ingeniería y estrategias pedagógicas innovadoras.'),


(5, 2, 'Premio nacional en informática por desarrollo de software educativo innovador que optimiza enseñanza de programación y análisis de datos con plataformas virtuales.'),
(6, 2, 'Reconocimiento por liderazgo académico, coordinando proyectos de innovación educativa, implementación de sistemas de gestión de calidad y mejora de procesos.'),
(7, 2, 'Desarrollo de software educativo aplicando simuladores, laboratorios virtuales y plataformas de evaluación, facilitando aprendizaje activo y competencias profesionales.'),
(8, 2, 'Publicación en revista internacional sobre innovación en informática y educación, destacando metodologías activas y análisis de desempeño estudiantil.'),


(9, 3, 'Premio a la investigación en física aplicada por experimentación en laboratorios universitarios y desarrollo de materiales educativos que fortalecen la formación científica.'),
(10, 3, 'Reconocimiento por liderazgo en proyectos educativos y coordinación de laboratorios de ingeniería con énfasis en energías renovables y sostenibilidad.'),
(11, 3, 'Publicación de libro sobre nanotecnología y energías renovables con capítulos prácticos, promoviendo aprendizaje experimental e investigación aplicada.'),
(12, 3, 'Ponencia internacional sobre materiales avanzados y técnicas de laboratorio aplicadas a formación de estudiantes de física e ingeniería.'),


(13, 4, 'Premio a la gestión administrativa educativa por optimización de procesos, planificación estratégica y coordinación de recursos humanos y materiales.'),
(14, 4, 'Reconocimiento por liderazgo académico en implementación de políticas institucionales, mejora continua y eficiencia operativa en programas educativos.'),
(15, 4, 'Publicación de libro sobre administración educativa con capítulos sobre planificación, coordinación, evaluación y mejora de procesos.'),
(16, 4, 'Capítulo de libro sobre innovación y liderazgo en administración académica, incluyendo casos de éxito y estrategias aplicadas.');


-- ===================== PARTICIPATIONS =====================
INSERT INTO participations (cv_id, institution, period, level_participation)
VALUES
(1, 'SEP', 2015, 1),
(1, 'CONACYT', 2017, 2),
(1, 'UNESCO', 2019, 3),
(1, 'UASLP', 2021, 4),

(2, 'SEP', 2014, 1),
(2, 'CONACYT', 2016, 3),
(2, 'UNESCO', 2018, 4),
(2, 'UASLP', 2020, 8),

(3, 'SEP', 2013, 1),
(3, 'CONACYT', 2015, 2),
(3, 'UNESCO', 2017, 7),
(3, 'UASLP', 2019, 6),

(4, 'SEP', 2010, 7),
(4, 'CONACYT', 2012, 9),
(4, 'UNESCO', 2014, 5),
(4, 'UASLP', 2016, 3);

-- ===================== AWARDS =====================
INSERT INTO awards (cv_id, description)
VALUES

(1, 'Premio estatal de docencia en matemáticas por implementar estrategias innovadoras de enseñanza, evaluación formativa y metodologías digitales y colaborativas que mejoraron significativamente la calidad educativa y desempeño estudiantil.'),
(1, 'Reconocimiento universitario a la investigación por publicaciones académicas, desarrollo de materiales didácticos y coordinación de programas educativos que fortalecieron la formación integral'),
(1, 'Premio nacional por innovación educativa, destacando diseño de planes de estudio, integración de tecnologías y metodologías activas que promovieron aprendizaje significativo, retención y eficiencia'),
(1, 'Distinción académica nacional por liderazgo en proyectos educativos, desarrollo de recursos didácticos y mejora continua de programas de educación superior, contribuyendo a la excelencia académica'),

(2, 'Premio nacional en informática por desarrollo de software educativo innovador que optimiza enseñanza de programación, análisis de datos y ciberseguridad, integrando plataformas virtuales y metodologías'),
(2, 'Reconocimiento por liderazgo académico en implementación de proyectos de innovación educativa, mejora de procesos administrativos y coordinación de programas, fomentando participación docente, eficiencia operativa'),
(2, 'Premio por investigación aplicada en informática educativa, destacando uso de inteligencia artificial, análisis de desempeño estudiantil y metodologías activas que fortalecen la formación integral.'),
(2, 'Distinción por desarrollo de recursos tecnológicos y software educativo innovador que facilita aprendizaje activo, competencias profesionales y mejora continua en programas de ingeniería y tecnología de la institución.'),

(3, 'Premio a la investigación en física aplicada por experimentación avanzada en laboratorios universitarios, desarrollo de materiales educativos y coordinación de proyectos que fortalecen la formación científica'),
(3, 'Reconocimiento por liderazgo en coordinación de laboratorios de ingeniería y proyectos de energías renovables, integrando metodologías experimentales y aplicadas que incrementan la eficiencia, calidad educativa'),
(3, 'Distinción académica por publicaciones en nanotecnología y energías renovables, integrando investigación aplicada y docencia, con impacto en la formación integral de estudiantes y fortalecimiento de la reputación'),
(3, 'Premio nacional por liderazgo educativo y mejora continua de programas de física e ingeniería, aplicando innovación, planificación estratégica y coordinación de recursos que elevan la calidad educativa y el desempeño estudiantil.'),

(4, 'Premio a la gestión administrativa educativa por optimización de procesos, planificación estratégica y coordinación de recursos humanos y materiales, mejorando eficiencia operativa, cumplimiento institucional'),
(4, 'Reconocimiento por liderazgo académico en implementación de políticas institucionales, gestión de calidad, seguimiento de procesos y mejora continua en programas educativos, aumentando eficiencia'),
(4, 'Premio por innovación en administración educativa, incluyendo implementación de procesos estandarizados, coordinación de proyectos estratégicos, optimización de recursos y mejora continua de la eficiencia'),
(4, 'Distinción académica nacional por liderazgo y mejora continua en administración de programas educativos, implementación de metodologías innovadoras y estrategias de planificación ');


-- ===================== CONTRIBUTIONS_TO_PE =====================
INSERT INTO contributions_to_pe (cv_id, description)
VALUES
(1, 'La contribución en gestión administrativa educativa se centra en optimizar los procesos internos de la institución, promoviendo la eficiencia en la toma de decisiones y la coordinación entre departamentos. Incluye la implementación de políticas claras, seguimiento de indicadores de desempeño, y la creación de sistemas de control que aseguren la correcta administración de recursos humanos, financieros y materiales. Asimismo, se destacan las acciones de planificación estratégica y la alineación de los objetivos institucionales con las necesidades de la comunidad educativa. La contribución abarca la digitalización de procesos, el establecimiento de protocolos administrativos, y la capacitación continua del personal administrativo para asegurar la calidad en el servicio educativo. La evaluación periódica de procedimientos, la mejora continua y la adaptación a cambios normativos y tecnológicos son pilares fundamentales de esta gestión, buscando un impacto positivo en la eficiencia operativa y la satisfacción de estudiantes, docentes y personal administrativo. También se incluyen estrategias de comunicación interna que fomentan la transparencia, la rendición de cuentas y el compromiso institucional. La contribución contempla la integración de buenas prácticas de gestión, la estandarización de procesos críticos y la elaboración de manuales administrativos que faciliten la continuidad institucional y reduzcan riesgos operativos. Además, promueve la coordinación interdepartamental, la supervisión de indicadores de calidad y la elaboración de reportes periódicos que apoyen la toma de decisiones. Este enfoque garantiza que la administración educativa no solo cumpla con los requerimientos normativos, sino que también sea proactiva, innovadora y orientada a la mejora constante, fortaleciendo la calidad educativa y la sostenibilidad de la institución en el largo plazo.'),
(2, 'La contribución en gestión administrativa educativa implica el diseño, implementación y seguimiento de estrategias que aseguren el funcionamiento eficiente y efectivo de la institución. Esta labor contempla la planificación de recursos humanos, financieros y materiales, asegurando su correcta asignación y utilización. Incluye el desarrollo de políticas internas, la estandarización de procesos, y la creación de herramientas de control que permitan medir y evaluar el desempeño administrativo. También se enfoca en la optimización de la comunicación entre áreas, promoviendo la coordinación y colaboración para alcanzar los objetivos estratégicos de la institución. La contribución considera la implementación de sistemas tecnológicos para agilizar procesos, reducir errores y garantizar la trazabilidad de acciones administrativas. La capacitación y desarrollo profesional del personal administrativo es un elemento central, asegurando que estén preparados para responder a cambios normativos, tecnológicos y educativos. Asimismo, se destacan acciones de evaluación y mejora continua, mediante la revisión periódica de procedimientos, la actualización de manuales internos, y la incorporación de buenas prácticas reconocidas a nivel nacional e internacional. Este enfoque permite que la gestión administrativa educativa sea transparente, eficiente, innovadora y centrada en resultados, contribuyendo al fortalecimiento de la calidad educativa, la satisfacción de la comunidad académica y la sostenibilidad institucional a largo plazo.'),
(3, 'La contribución en gestión administrativa educativa se orienta a fortalecer la eficiencia, la transparencia y la eficacia de los procesos institucionales, garantizando un impacto positivo en la calidad educativa. Incluye la planificación estratégica de recursos humanos, financieros y materiales, la definición de responsabilidades claras y la implementación de protocolos administrativos que aseguren el cumplimiento de metas y objetivos institucionales. Esta contribución también abarca la evaluación constante de procedimientos, la incorporación de indicadores de desempeño y la generación de reportes que faciliten la toma de decisiones basada en evidencia. La modernización de los procesos mediante la digitalización y el uso de herramientas tecnológicas permite optimizar tiempos, reducir errores y mejorar la gestión de información. Asimismo, se promueve la capacitación continua del personal administrativo para garantizar que sus competencias estén alineadas con los cambios normativos, pedagógicos y tecnológicos. Se enfatiza la mejora continua mediante auditorías internas, ajustes en los procesos y la aplicación de buenas prácticas de gestión reconocidas en el ámbito educativo. Además, se fomenta una cultura de colaboración interdepartamental, comunicación efectiva y responsabilidad institucional, asegurando que los esfuerzos de gestión administrativa contribuyan directamente a la eficiencia operativa, la sostenibilidad institucional y la excelencia educativa.'),
(4, 'La contribución en gestión administrativa educativa implica desarrollar e implementar estrategias que optimicen la operación de la institución y garanticen la calidad de los servicios educativos. Este enfoque considera la planificación, coordinación y supervisión de los recursos humanos, financieros y materiales, asegurando su correcta utilización y sostenibilidad. La contribución también incluye la elaboración de políticas internas, procedimientos estandarizados y manuales administrativos que faciliten la continuidad y coherencia institucional. Se promueve la adopción de tecnologías y sistemas de información que agilicen procesos, mejoren la trazabilidad de decisiones y reduzcan riesgos operativos. Asimismo, se enfatiza la capacitación constante del personal administrativo para que adquiera competencias actualizadas, cumpla con normativas vigentes y se adapte a innovaciones educativas. La evaluación periódica de procedimientos, la recopilación de indicadores de desempeño y la revisión de prácticas permiten identificar oportunidades de mejora y fomentar la eficiencia organizacional. Este tipo de contribución fortalece la coordinación interdepartamental, promueve la transparencia y la rendición de cuentas, y asegura que la gestión administrativa educativa esté alineada con los objetivos estratégicos de la institución, generando un impacto positivo en la comunidad académica y contribuyendo al desarrollo sostenido del programa educativo.');
