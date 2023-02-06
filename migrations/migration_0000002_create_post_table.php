<?php

class migration_0000002_create_post_table
{

    public function up(){
        $db = \core\Application::$app->db;
        $SQL = "CREATE TABLE posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                image VARCHAR(255),
                user_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE 
                ) ENGINE=INNODB;";

        $db->pdo->exec($SQL);
    }

    public function down(){
        $db = \core\Application::$app->db;
        $SQL = "DROP TABLE posts;";

        $db->pdo->exec($SQL);
    }

}