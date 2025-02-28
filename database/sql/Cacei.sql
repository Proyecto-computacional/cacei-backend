CREATE TABLE Cv (
    Cv_id BIGINT NOT NULL,
    professor_number INT UNIQUE,
    update_date DATE,
    professor_name VARCHAR(25),
    age INT,
    birth_date DATE,
    actual_position VARCHAR(20),
    duration INT,
	PRIMARY KEY (Cv_id)
);

CREATE TABLE User_t (
    User_rpe VARCHAR(20) NOT NULL,
    User_mail VARCHAR(100) UNIQUE NOT NULL,
    User_role VARCHAR(20) NOT NULL,
    Cv_id BIGINT,
	PRIMARY KEY (User_rpe),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Frame_of_reference (
    Frame_id int NOT NULL,
    Frame_name VARCHAR(20) NOT NULL,
	PRIMARY KEY (Frame_id)
);

CREATE TABLE Category (
    Category_id int NOT NULL,
	Category_name VARCHAR(50) NOT NULL,
	Frame_id int NOT NULL,
	PRIMARY KEY (Category_id),
	FOREIGN KEY (Frame_id) REFERENCES Frame_of_reference(Frame_id)
);

CREATE TABLE Section_t (
    Section_id int NOT NULL,
	Category_id int NOT NULL,
	Section_name VARCHAR(25) NOT NULL,
	Section_description VARCHAR(50) NOT NULL,
	PRIMARY KEY (Section_id),
	FOREIGN KEY (Category_id) REFERENCES Category(Category_id)
);

CREATE TABLE Standard (
    Standard_id int NOT NULL,
	Section_id int NOT NULL,
	Standard_name VARCHAR(25) NOT NULL,
	Standard_description VARCHAR(50) NOT NULL,
	Is_transversal bool NOT NULL,
	Help VARCHAR(255),
	PRIMARY KEY (Standard_id),
	FOREIGN KEY (Section_id) REFERENCES Section_t(Section_id)
);

CREATE TABLE Evidence (
	Evidence_id int NOT NULL,
	Standard_id int NOT NULL,
	User_rpe VARCHAR(20) NOT NULL,
	Group_id int NOT NULL,
	Process_id int NOT NULL,
	Due_date DATE NOT NULL,
	PRIMARY KEY (Evidence_id),
	FOREIGN KEY (Standard_id) REFERENCES Standard(Standard_id)
);

CREATE TABLE Reviser (
    Reviser_id BIGSERIAL NOT NULL,
    User_rpe VARCHAR(20) UNIQUE NOT NULL,
    Evidence_id BIGINT,
	PRIMARY KEY (Reviser_id),
	FOREIGN KEY (User_rpe) REFERENCES User_t(User_rpe),
	FOREIGN KEY (Evidence_id) REFERENCES Evidence(Evidence_id)
);

CREATE TABLE Education (
    Education_id BIGSERIAL NOT NULL,
    Cv_id BIGINT NOT NULL,
    Institution VARCHAR(30),
    Degree_obtained VARCHAR(1),
    Obtained_year INT,
    Professional_license VARCHAR(8),
    Degree_name VARCHAR(50),
	PRIMARY KEY (Education_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Teacher_training (
    Teacher_training_id BIGSERIAL NOT NULL,
    Title_certification VARCHAR(50),
    Obtained_year INT,
    Institution_country VARCHAR(50),
    Hours INT,
    Cv_id BIGINT,
	PRIMARY KEY (Teacher_training_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Disciplinary_update (
    Disciplinary_update_id BIGSERIAL NOT NULL,
    Cv_id BIGINT ,
    Title_certification VARCHAR(50),
    Year_certification INT,
    Institution_country VARCHAR(50),
    Hours INT,
	PRIMARY KEY (Disciplinary_update_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Academic_management (
    Academic_management_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Job_position VARCHAR(100),
    Institution VARCHAR(50),
    start_date DATE,
    end_date DATE,
	PRIMARY KEY (Academic_management_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Academic_product (
    Academic_product_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Academic_product_number INT,
    Description VARCHAR(150),
    PRIMARY KEY (Academic_product_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Laboral_experience (
    Laboral_experience_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Company_name VARCHAR(50),
    Position VARCHAR(50),
    Start_date DATE,
    End_date DATE,
	PRIMARY KEY (Laboral_experience_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);


CREATE TABLE Engineering_design (
    Engineering_design_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Institution VARCHAR(30),
    Period INT,
    Level_experience VARCHAR(20),
	PRIMARY KEY (Engineering_design_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Professional_achievement (
    Achievement_id int NOT NULL,
    Cv_id BIGINT,
    Description VARCHAR(255),
	PRIMARY KEY (Achievement_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Participation (
    Participation_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Institution VARCHAR(30),
    Period INT,
    Level_participation VARCHAR(20),
	PRIMARY KEY (Participation_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Award (
    Award_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Description VARCHAR(255),
	PRIMARY KEY (Award_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Contribution_to_PE (
    Contribution_id BIGSERIAL NOT NULL,
    Cv_id BIGINT,
    Description VARCHAR(255),
	PRIMARY KEY (Contribution_id),
	FOREIGN KEY (Cv_id) REFERENCES Cv(Cv_id)
);

CREATE TABLE Area (
    Area_id VARCHAR(20) NOT NULL,
    Area_name VARCHAR(20) NOT NULL,
	PRIMARY KEY (Area_id)
);

CREATE TABLE Career (
    Career_id VARCHAR(20) NOT NULL,
    Area_id VARCHAR(20) NOT NULL,
    Career_name VARCHAR(20) NOT NULL,
	PRIMARY KEY (Career_id),
	FOREIGN KEY (Area_id) REFERENCES Area(Area_id)
);

CREATE TABLE Accreditation_process (
    Process_id int NOT NULL,
    Career_id VARCHAR(20) NOT NULL,
    Frame_id INT,
    start_date DATE,
    end_date DATE,
    due_date DATE,
	PRIMARY KEY (Process_id),
	FOREIGN KEY (Career_id) REFERENCES Career(Career_id),
	FOREIGN KEY (Frame_id) REFERENCES Frame_of_reference(Frame_id)
);

CREATE TABLE Subject (
	Subject_id int NOT NULL,
	Subject_name VARCHAR(50) NOT NULL,
	Career_id VARCHAR(20) NOT NULL,
	PRIMARY KEY (Subject_id),
	FOREIGN KEY (Career_id) REFERENCES Career(Career_id)
);

CREATE TABLE Group_t (
	Group_id int NOT NULL,
	Semester VARCHAR(15) NOT NULL,
	Type_a bool NOT NULL,
	Period_a VARCHAR(25) NOT NULL,
	Subject_id int NOT NULL,
	Hour_a VARCHAR(5) NOT NULL,
	PRIMARY KEY (Group_id),
	FOREIGN KEY (Subject_id) REFERENCES Subject(Subject_id)
);

CREATE TABLE Status (
	Status_id int NOT NULL,
	Status_description VARCHAR(15) NOT NULL,
	User_rpe VARCHAR(20) NOT NULL,
	Status_date DATE NOT NULL,
	Feedback VARCHAR(255),
	PRIMARY KEY (Status_id),
	FOREIGN KEY (User_rpe) REFERENCES User_t(User_rpe)
);

CREATE TABLE File_t (
	File_id int NOT NULL,
	File_url VARCHAR(255) NOT NULL,
	Upload_date DATE NOT NULL,
	Evidence_id int NOT NULL,
	Justification VARCHAR(255),
	PRIMARY KEY (File_id),
	FOREIGN KEY (Evidence_id) REFERENCES Evidence(Evidence_id)
);

CREATE TABLE Notification (
	Notification_id int NOT NULL,
	Title VARCHAR(20) NOT NULL,
	Evidence_id int NOT NULL,
	Notification_date DATE NOT NULL,
	User_rpe VARCHAR(20) NOT NULL,
	Description VARCHAR(20),
	Seen bool NOT NULL,
	PRIMARY KEY (Notification_id),
	FOREIGN KEY (User_rpe) REFERENCES User_t(User_rpe),
	FOREIGN KEY (Evidence_id) REFERENCES Evidence(Evidence_id)
);

CREATE ROLE administrador;
CREATE ROLE directivo;
CREATE ROLE profesor_responsable;
CREATE ROLE departamento_universitario;
CREATE ROLE profesor;
CREATE ROLE personal_apoyo;

GRANT ALL PRIVILEGES ON DATABASE "Cacei" TO administrador;

GRANT CONNECT ON DATABASE "Cacei" TO directivo;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO directivo;

GRANT CONNECT ON DATABASE "Cacei" TO profesor_responsable;
GRANT SELECT, INSERT, UPDATE ON Evidence, File_t TO profesor_responsable;

GRANT CONNECT ON DATABASE "Cacei" TO departamento_universitario;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO departamento_universitario;

GRANT CONNECT ON DATABASE "Cacei" TO profesor;
GRANT SELECT, INSERT ON Evidence TO profesor;

GRANT CONNECT ON DATABASE "Cacei" TO personal_apoyo;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO personal_apoyo;
