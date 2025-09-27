<?php
namespace HtmlSocialShare;

interface ProfileManagerInterface
{
    public function createProfile(array $data): int;

    public function getProfile(int $id): ?array;

    public function updateProfile(int $id, array $data): bool;

    public function deleteProfile(int $id): bool;

    public function listProfiles(): array;
}
