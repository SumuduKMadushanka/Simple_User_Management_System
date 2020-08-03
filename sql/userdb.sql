DROP DATABASE IF EXISTS userdb;
CREATE DATABASE userdb;
USE userdb;

DROP TABLE IF EXISTS users;
CREATE TABLE users(
    user_id INT(11) AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    hashed_password VARCHAR(40) NOT NULL,
    salt INT(11) NOT NULL,
    last_login DATETIME,
    is_deleted TINYINT(1) DEFAULT 0,
    PRIMARY KEY (user_id)
);

INSERT INTO users 
(first_name, last_name, email, hashed_password, salt) 
VALUES
('Sumudu', 'Madushanka', 'sumudu@gmail.com', '23de86f8042bc97da1872f5dd9225207e8567db1', 1675969432),
('Pradeep', 'Madushanka', 'pradeep@gmail.com', 'b0bdbc52a6d97bb76f638a73eefd9f2c23b36fea', 1188417573),
('Kamal', 'Perera', 'kamal@gmail.com', '66d160d5e8e4fd5e337fd8bd39dfd60f31fcc560', 107585526),
('Keshani', 'Bagya', 'keshani@gmail.com', '45f4bea4a27d6a7c9ec4d895d65645e0721e1b50', 2002196939),
('Kanchana', 'Sandamali', 'kanchana@gmail.com', 'e4da74e29c4a767097b811c092cdef62aa9ccd1c', 3748935),
('Samadhi', 'Abesinghe', 'samadhi@gmail.com', '05c226281473e6515ad5e3828be113aab0e2421c', 410972821),
('Sunil', 'Amarasena', 'sunil@gmail.com', 'f3c9228ad00b0bb04f36aea5f513c08270e681f6', 383224340);