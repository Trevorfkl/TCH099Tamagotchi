<?php

class Plant implements JsonSerializable, Idable
{
	private ?int $id;
	private string $name;
	private array $plantStages;

	public function __construct(
		?int $id,
		string $name, 
		array $plantStages
	) {
		$this->id = $id;
		$this->name = $name;
		$this->plantStages = $plantStages;
	}

	// Getters
	public function getId(): ?int 
	{
		return $this->id;
	}
	public function getName(): string 
	{
		return $this->name;
	}
	public function getPlantStages(): array 
	{
		return $this->plantStages;
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
	public function setPlantStages(array $plantStages): void 
	{
		$this->plantStages = $plantStages;
	}

	// Serialize
	public function jsonSerialize(): mixed
	{
		return [
			"id" => $this->id,
			"name" => $this->name,
			"plantStages" => $this->plantStages
		];	
	}

}
?>