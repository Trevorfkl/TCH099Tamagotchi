<?php

class Semester implements JsonSerializable, Idable {
    private ?int $id;
    private int $userId;
    private string $season;
    private int $year;
    private DateTime $startDate;
    private DateTime $endDate;
    private array $courses;

    public function __construct(
        ?int $id,
        int $userId,
        string $season,
        int $year,
        DateTime $startDate,
        DateTime $endDate,
        array $courses
    ) {
        $this->id =  $id;
        $this->userId = $userId;
        $this->season =  $season;
        $this->year =  $year;
        $this->startDate =  $startDate;
        $this->endDate =  $endDate;
        $this->courses = $courses;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getSeason(): string
    {
        return $this->season;
    }
    public function getYear(): int
    {
        return $this->year;
    }
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getCourses(): array 
    {
        return $this->courses;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }
    public function setSeason($season): void
    {
        $this->season = $season;
    }
    public function setYear($year): void
    {
        $this->year = $year;
    }
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }
    public function setClasses(array $courses): void
    {
        $this->courses = $courses;
    }


    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "userId" => $this->userId,
            "season" => $this->season,
            "year" => $this->year,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
            "courses" => $this->courses,
        ];
    }
}

?>