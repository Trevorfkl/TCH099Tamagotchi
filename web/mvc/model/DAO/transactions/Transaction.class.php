<?php

class Transaction
{
    /**
     * Prend un array de queries depuis les DAOs et les exécute dans une transaction. 
     * Si une des queries échoue, la transaction est annulée.
     *
     * @param callable(PDO):mixed[] $queries array de fonctions DAO qui prennent une connexion PDO.
     * @return TransactionResult Un objet contenant le succes d'une transaction, et ces resultats dans un key-value array.
     */
    public static function begin(array $queries): TransactionResult
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        try {
            $connexion->beginTransaction();

            $transactionResult = new TransactionResult(true, [], null);
    
            foreach ($queries as $name => $query) {
                if (!is_callable($query)) {
                    throw new Exception("Query $name is not callable");
                }

                $result = $query($connexion);
                $transactionResult->addQueryResult($name, $result);
            }
            $connexion->commit();
            ConnexionBD::close();
            return $transactionResult;

        } catch (Exception $e) {
            $connexion->rollBack();
            ConnexionBD::close();
            return new TransactionResult(false, [], "Erreur lors de la transaction");
        }
    }
}

?>