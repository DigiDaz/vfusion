CREATE TABLE IF NOT EXISTS calls(
	id INT NOT NULL AUTO_INCREMENT,
	uuid varchar(100),
	number_from varchar(100),
	number_to varchar(100),
	start_time datetime,
	end_time datetime,
	call_event varchar(100),	
	type varchar(100),
	answered int DEFAULT -1,			
	PRIMARY KEY (id)

) ENGINE = InnoDB, DEFAULT CHARACTER SET utf8;