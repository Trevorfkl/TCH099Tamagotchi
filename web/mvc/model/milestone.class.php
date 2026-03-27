<?php

class Milestone implements JsonSerializable, Idable {
    private ?int $id;
    private ?int $projectId;

    private string $name;
    private int $Z;

    public function __construct(
        ?int $id, 
        ?int $projectId,
        string $name,   
        int $Z
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->name = $name;
        $this->Z = $Z;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getProjectId(): ?int
    {
        return $this->projectId;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getZ(): int
    {
        return $this->Z;
    }

    // Setters
    public function setId($id): void
    {
        $this->id = $id;
    }
    public function setProjectId(int $projectId): void
    {
        $this->projectId = $projectId;
    }
    public function setName($name): void
    {
        $this->name = $name;
    }
    public function setZ($Z): void
    {
        $this->Z = $Z;
    }

    // Serialize
    public function jsonSerialize(): mixed 
    {
        return [
            "id" => $this->id,
            "projectId"=> $this->projectId,
            "name" => $this->name,
            "Z" => $this->Z
        ];
    }
}

?>