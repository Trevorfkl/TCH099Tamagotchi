<?php

class TransactionResult
{
    private bool $success;
    private array $queryResults;
    private ?string $errorMessage;

    public function __construct(bool $success, array $queryResults, ?string $errorMessage = null)
    {
        $this->success = $success;
        $this->queryResults = $queryResults;
        $this->errorMessage = $errorMessage;
    }

    

    public static function emptyResult(): TransactionResult
    {
        return new TransactionResult(true, [], null);
    }

    public static function errorResult($errorMessage = null): TransactionResult
    {
        return new TransactionResult(false, [], ($errorMessage === null) ? "Erreur non specifiee" : $errorMessage);
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

    public function addQueryResult(string $queryName, object $result): void
    {
        $this->queryResults[$queryName] = $result;
    }

}

?>