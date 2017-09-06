


CREATE TABLE UserLogin (
	username varchar(25) NOT NULL,
	password char(40) NOT NULL,
	PRIMARY KEY(username)
);

CREATE TABLE UserInfo (
	password char(40) NOT NULL,
	userID int(10) NOT NULL AUTO_INCREMENT,
	username varchar(25) NOT NULL,
	PRIMARY KEY(userID),
	FOREIGN KEY (username) REFERENCES UserLogin(username)
);

CREATE TABLE Algorithms (
	algorithmID int(10) NOT NULL AUTO_INCREMENT,
        algorithmName varchar(30),
	algorithm varchar(60) NOT NULL,
	PRIMARY KEY(algorithmID)
);

CREATE TABLE History (
	historyID int(10) NOT NULL,
	userID int(10) NOT NULL,
	algorithmID int(10) NOT NULL,
	lastAccessedTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        userInput varchar(100) NOT NULL,
	PRIMARY KEY(historyID),
	FOREIGN KEY (userID) REFERENCES UserInfo(userID),
	FOREIGN KEY (algorithmID) REFERENCES Algorithms(algorithmID)
);



--Add Algorithms
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Area of a Rectangle', 'L*W');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Area of a Circle', '3.141592*r^2');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Area of a Triangle', '1/2*b*h');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Area of a Trapezoid', '1/2*(b1+b2)*h');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Volume of a Sphere', '(4/3)*3.141592*r^3*');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Surface Area of a Sphere', '4*3.141592*r^2');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Surface Area of a Rectangular Prism', '2*(wh+lw+lh)');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Volume of a Rectangular Prism', 'l*w*h');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Surface Area of a Square Based Pyramid', '2*b*s+b^2');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Volume of a Square Based Pyramid', '(1/3)*b^2*h');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Tips', '0.15*bill');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Area of a Circle Sector', '(n/360)*3.141592*r^2');
INSERT INTO algorithms (algorithmName, algorithm) VALUES ('Length of an Arc', '(n/360)*2*3.141592*r');


