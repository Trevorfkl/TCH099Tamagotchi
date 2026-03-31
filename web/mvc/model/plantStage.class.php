<?php

class PlantStage implements JsonSerializable, Idable
{
    private ?int $id;
    private ?int $plantId;
    private string $image;
    private int $Z;

    public function __construct(
        ?int $id,
        ?int $plantId,
        string $image,
        int $Z
    ) {
        $this->id = $id;
        $this->plantId = $plantId;
        $this->image = $image;
        $this->Z = $Z;
    }

    // Getters
    
    public function getId(): ?int 
    {
        return $this->id;
    }
    public function getPlantId(): ?int 
    {
        return $this->plantId;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getZ(): int
    {
        return $this->Z;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setPlantId($plantId): void
    {
        $this->plantId = $plantId;
    }

    public function setImage($image): void
    {
        $this->image = $image;
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
            "plantId"=> $this->plantId,
            "image" => $this->image,
            "Z" => $this->Z
        ];
    }
}

?>
