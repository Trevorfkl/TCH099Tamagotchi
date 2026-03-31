<?php

class Role implements JsonSerializable, Idable{
    private ?int $id;
    private string $roleName;

    public function __construct(?int $id, string $roleName)
    {
        $this->id = $id;
        $this->roleName = $roleName;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    // Setters
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setRoleName(string $roleName): void
    {
        $this->roleName = $roleName;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'roleName' => $this->roleName,
        ];
    }
}

?>