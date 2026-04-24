<?php

class Course implements JsonSerializable, Idable
{
    private ?int $id;
    private string $name;
    private string $code;
    private ?int $semesterId;
    private array $projects;


    public function __construct(
        ?int $id,
        ?int $semesterId,
        string $name,
        string $code,
        array $projects
    ) {
        $this->id = $id;
        $this->semesterId = $semesterId;
        $this->name = $name;
        $this->code = $code;
        $this->projects = $projects;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getCode(): string {
        return $this->code;
    }
    public function getProjects(): array {
        return $this->projects;
    }
     public function getSemesterId(): int {
        return $this->semesterId;
    }

    // Setters
    public function setId($id): void 
    {
        $this->id = $id;
    }
    public function setName($name): void 
    {
        $this->name = $name;
    }
    public function setCode($code): void 
    {
        $this->code = $code;
    }
    public function setProjects(array $projects): void
    {
        $this->projects = $projects;
    }
    public function setSemesterId($semesterId): void {
        $this->semesterId = $semesterId;
    }

    // Serialize
    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "code" => $this->code,
            "projects"=> $this->projects,
            "semesterId"=> $this->semesterId
        ];
    }
}


?>