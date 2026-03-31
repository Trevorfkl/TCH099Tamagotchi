<?php

interface Idable {
    public function getId(): ?int;
    public function setId(int $id): void;
}

?>