<?php

class Transaction
{
    private array $queries;
    private static TransactionResult $transactionResult;
    public function __construct() {
        $this->queries = [];
        $this->transactionResult = new TransactionResult(false, [], "Aucune transaction effectuee");
    }

    /**
     * Prend un array de queries depuis les DAOs et les exécute dans une transaction. 
     * Si une des queries échoue, la transaction est annulée.
     *
     * @param callable(PDO):mixed[] $queries array de fonctions DAO qui prennent une connexion PDO.
     * @return TransactionResult Un objet contenant le succes d'une transaction, et ces resultats dans un key-value array.
     */
    public function begin(): void
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        try {
            ConnexionContext::set($connexion);

            $connexion->beginTransaction();

            $transactionResult = new TransactionResult(true, [], null);
    
            foreach ($this->queries as $name => $query) {
                if (!is_callable($query)) {
                    throw new Exception("Query $name is not callable");
                }

                $result = $query($connexion);
                $transactionResult->addQueryResult($name, $result);
            }
            $connexion->commit();
            $this->transactionResult = $transactionResult;

        } catch (Exception $e) {
            $connexion->rollBack();
            $this->transactionResult = new TransactionResult(false, [], "Erreur lors de la transaction");

        } finally {
            ConnexionContext::clear();
            ConnexionBD::close();
        }
    }

    public static function run(callable $callback): void
    {
        self::$transactionResult = TransactionResult::emptyResult();
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
        
        ConnexionContext::set($connexion);
        try {
            $transactionResult = $callback($connexion);
            $connexion->commit();
            self::$transactionResult = $transactionResult;
        } catch (Exception $e) {
            self::$transactionResult = TransactionResult::errorResult($e->getMessage());
            $connexion->rollBack();
        } finally {
            ConnexionContext::clear();
            ConnexionBD::close();
        }
        
    }

    public static function getResult(): TransactionResult
    {
        return self::$transactionResult;
    }

    public static function clearResult(): void
    {
        self::$transactionResult = TransactionResult::emptyResult();
    }

    public static function isSuccess(): bool
    {
        return self::$transactionResult->isSuccess();
    }

    public function addQuery(string $name, callable $query): void
    {
        $this->queries[$name] = $query;
    }

}

?>