<?php

class User implements JsonSerializable, Idable
{

    private ?int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $hashedPassword;
    private Role $role;
    private array $semesters;

    public function __construct(
        ?int $id, 
        string $firstName, 
        string $lastName, 
        string $email, 
        string $hashedPassword, 
        Role $role,
        array $semesters
    ) { 
        $this->id = $id ?? null;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->hashedPassword = $hashedPassword;
        $this->role = $role;
        $this->semesters = $semesters;
    }

    public function getId(): int 
    {
        return $this->id;
    }
    public function getFirstName(): string 
    {
        return $this->firstName;
    }
    public function getLastName(): string 
    {
        return $this->lastName;
    }
    public function getEmail(): string 
    {
        return $this->email;
    }
    public function getHashedPassword(): string 
    {
        return $this->hashedPassword;
    }
    public function getRole(): Role 
    {
        return $this->role;
    }
    public function getsemesters(): array 
    {
        return $this->semesters;
    }

    public function setId(int $id): void 
    {
        $this->id = $id;
    }
    public function setFirstName(string $firstName): void 
    {
        $this->firstName = $firstName;
    }
    public function setLastName(string $lastName): void 
    {
        $this->lastName = $lastName;
    }
    public function setEmail(string $email): void 
    {
        $this->email = $email;
    }
    public function setHashedPassword(string $hashedPassword): void 
    {
        $this->hashedPassword = $hashedPassword;
    }
    public function setRole(Role $role): void 
    {
        $this->role = $role;
    }
    public function setsemesters(array $semesters): void 
    {
        $this->semesters = $semesters;
    }

     // Method to verify password
     public function verifyPassword(string $password): bool
     {
         // Assuming $this->password is a hashed password (e.g., bcrypt)
         return password_verify($password, $this->hashedPassword);
     }

     // Méthode pour hacher le mot de passe 
     public function hashPassword(): void {
        $this->hashedPassword = password_hash($this->hashedPassword, PASSWORD_BCRYPT);
    }
    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "hashedPassword" => $this->hashedPassword,
            "role" => $this->role,
            "semesters" => $this->semesters
        ];
    }
}

?>