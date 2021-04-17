<?php

namespace App\Models\Interfaces;

interface MovieInterface
{
    public function getName(): ?string;

    public function getDvdId(): ?string;

    public function getTags(): array;

    public function getActresses(): array;

    public function isDownloadable(): bool;
}
