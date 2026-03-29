<?php

class TransactionResult
{
    private bool $success = true;
    private array $queryResults;
    private ?string $errorMessage;

    public function __construct(array $queryResults, ?string $errorMessage = null)
    {
        $this->queryResults = $queryResults;
        $this->errorMessage = $errorMessage;
    }

    

    public static function emptyResult(): TransactionResult
    {
        return new TransactionResult([], "Pas de transaction.");
    }

    public static function errorResult($errorMessage = null): TransactionResult
    {
        $tr = new TransactionResult([], ($errorMessage === null) ? "Erreur non specifiee" : $errorMessage);
        $tr->setUnsuccessful();
        return $tr;
    }

    public function setUnsuccessful(): void 
    {
        $this->success = false;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getQueryResults(): array
    {
        return $this->queryResults;
    }

    public function addQueryResult(string $queryName, mixed $result): void
    {
        if (array_key_exists($queryName, $this->queryResults)) {
            throw new Exception("Clé existe déja dans transaction.");
        }
        $this->queryResults[$queryName] = $result;
    }

}

?>