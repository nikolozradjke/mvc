<?php

namespace core;

class Database
{
    public \PDO $pdo;
    public $query;

    public function __construct(array $config){
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $connection = $this->pdo;

        $this->query = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($connection)
        {
            $statement = $connection->prepare($queryString);
            $statement->execute($queryParameters);

            // when the query is fetchable return all results and let hydrahon do the rest
            // (there's no results to be fetched for an update-query for example)
            if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface)
            {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
            // when the query is a instance of a insert return the last inserted id
            elseif($query instanceof \ClanCats\Hydrahon\Query\Sql\Insert)
            {
                return $connection->lastInsertId();
            }
            // when the query is not a instance of insert or fetchable then
            // return the number os rows affected
            else
            {
                return $statement->rowCount();
            }
        });
    }

    public function applyMigrations(){
        $this->createMigrationsTable();
        $applyiedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR.'/migrations');

        $applyableMigrations = array_diff($files, $applyiedMigrations);
        $newMigrations = [];
        foreach($applyableMigrations as $mig){
            if($mig === '.' || $mig === '..'){
                continue;
            }

            require_once Application::$ROOT_DIR.'/migrations/'.$mig;
            $migName = pathinfo($mig, PATHINFO_FILENAME);
            $instance = new $migName();
            $this->log("Migrating $mig" . PHP_EOL);
            $instance->up();
            $this->log("Migrated $mig" . PHP_EOL);
            $newMigrations[] = ['migration' => $mig];
        }
        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log('Nothing to migrate!');
        }

    }

    public function createMigrationsTable(){
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations(
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            migration VARCHAR(255),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=INNODB;");
    }

    public function getAppliedMigrations(){
        $appliedMigrations = $this->pdo->prepare("SELECT migration FROM migrations");
        $appliedMigrations->execute();

        return $appliedMigrations->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations($migrations){
        $this->query
            ->table('migrations')
            ->insert($migrations)
            ->execute();

        echo 'DONE';
    }

    protected function log($message){
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
}