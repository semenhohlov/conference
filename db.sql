/* MySQL */
CREATE TABLE countries (
    id int AUTO_INCREMENT PRIMARY KEY,
    name char(255),
    lat float,
    lon float,
    zoom float
) ENGINE = InnoDB;
CREATE TABLE conferences (
    id int AUTO_INCREMENT PRIMARY KEY,
    title char(255) not null,
    conf_date DATETIME,
    adress char(255),
    lat float,
    lon float,
    country_id int not null,
    CONSTRAINT `fk_conference_country`
    	FOREIGN KEY (country_id) REFERENCES countries (id)
)ENGINE = InnoDB;

/* PostgreSQL */
CREATE TABLE countries (
    id serial PRIMARY KEY,
    name char(255),
    lat float,
    lon float,
    zoom float
);
CREATE TABLE conferences (
    id serial PRIMARY KEY,
    title char(255) not null,
    conf_date TIMESTAMP,
    adress char(255),
    lat float,
    lon float,
    country_id int not null,
    	FOREIGN KEY (country_id) REFERENCES countries (id)
);