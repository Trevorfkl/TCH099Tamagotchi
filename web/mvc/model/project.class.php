<?php

include_once("./model/utils/projectUtils.class.php");

class Project implements JsonSerializable, Idable  
{
	private ?int $id;
	private string $name;
	private string $dueDate;
	private string $status; // en cours, complete, echoue
	// private ?Course $course;
	private Plant $plant;
    private ?int $courseId;
    private ?int $plantId;
	private ?int $currentMilestoneIndex;
	private array $milestones; // 0 to 10 milestones

    /* 
        On a besoin d'un moyen de déterminer quel plantStage devrait être affiché.
        Je pense que le meilleur moyen serait qu'un projet tient compte des indices
        qu'il devrait tirer des plantStages.
    */
	private array $stageIndicesToUse;
		
    /**
     * Crée un projet.  
     * @param ?int $id id du projet si présent
     * @param ?int $courseId id du cours associé au projet
     * @param string $name nom du projet
     * @param string $dueDate date de remise, format dd-mm-yyyy
     * @param string $status Statut du project, doit match le CHECK de la bd
     * @param int $plantId id de la plante associée au projet
     * param Course $course l'object associé au cours
     * @param ?int $currentMilestoneIndex l'index du milestone présentement actif.
     * @param Plant $plant l'object associé à la plante
     * @param Milestone[] $milestones array des milestones associés au project
     */
	public function __construct(
		?int $id,
        ?int $courseId,
        string $name,
        string $dueDate,
        string $status,
        // Course $course,
        int $plantId,
        ?int $currentMilestoneIndex, // devrait toujours etre saved
        Plant $plant,
        array $milestones
    ) {
	    $this->id = $id;
        $this->courseId = $courseId;
		$this->name = $name;
		$this->dueDate = $dueDate;
		$this->status = $status;
		// $this->course = $course;
		// $this->plant = $plant;
		$this->plantId = $plantId;	
        $this->currentMilestoneIndex = ($currentMilestoneIndex) ?? 0;
		$this->milestones = $milestones;		
		$this->stageIndicesToUse = ProjectUtils::evenlySpreadIndices(
            count($milestones), count($plant->getPlantStages()));
    }

    // ----------------------------------------------- //
    //                      GETTERS                    //
    // ----------------------------------------------- //
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCourseId(): ?int
    {
        return $this->courseId;
    }
    public function getPlantId(): int
    {
        return $this->plantId;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDueDate(): string
    {
        return $this->dueDate;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getCurrentMilestoneIndex(): int
    {
        return $this->currentMilestoneIndex;
    }
    public function getPlant(): Plant
    {
        return $this->plant;
    }
    public function getMilestones(): array
    {
        return $this->milestones;
    }
    public function getStageIndicesToUse(): array
    {
        return $this->stageIndicesToUse;
    }

    // ----------------------------------------------- //
    //                      SETTERS                    //
    // ----------------------------------------------- //
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setCourseId(?int $courseId): void
    {
        $this->courseId = $courseId;
    }
    public function setPlantId(int $plantId): void
    {
        $this->plantId = $plantId;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setDueDate(string $dueDate): void
    {
        $this->dueDate = $dueDate;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setPlant(Plant $plant): void 
    {
        $this->plant = $plant;
    }

    public function setCurrentMilestoneIndex(int $currentMilestoneIndex): void
    {
        $this->currentMilestoneIndex = $currentMilestoneIndex;
    }
    public function setMilestones(array $milestones): void
    {
        $this->milestones = $milestones;
    }
    public function setStageIndicesToUse(array $stageIndicesToUse): void
    {
        $this->stageIndicesToUse = $stageIndicesToUse;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'courseId' => $this->courseId,
            "plantId" => $this->plantId,
            'name' => $this->name,
            'dueDate' => $this->dueDate,
            'currentMilestoneIndex' => $this->currentMilestoneIndex, // devrait toujours etre saved
            'status' => $this->status,
            'milestones' => $this->milestones,
            'stageIndicesToUse' => $this->stageIndicesToUse
        ];
    }

}

?>