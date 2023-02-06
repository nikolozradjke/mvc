<?php

class migration_0000001_create_user_table
{

    public function up(){
        $db = \core\Application::$app->db;
        $SQL = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE,
                firstname VARCHAR(255),
                lastname VARCHAR(255),
                image VARCHAR(255),
                role INT DEFAULT 1,
                password VARCHAR(512),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=INNODB;";

        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = \core\Application::$app->db;
        $SQL = "DROP TABLE users;";

        $db->pdo->exec($SQL);
    }

}