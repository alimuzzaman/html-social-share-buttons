<?php
namespace HtmlSocialShare;

class ProfileManager implements ProfileManagerInterface
{
    private array $store = [];
    private int $nextId = 1;

    public function createProfile(array $data): int
    {
        $id = $this->nextId++;
        $this->store[$id] = $data;
        return $id;
    }

    public function getProfile(int $id): ?array
    {
        return $this->store[$id] ?? null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        if (! isset($this->store[$id])) {
            return false;
        }
        $this->store[$id] = $data;
        return true;
    }

    public function deleteProfile(int $id): bool
    {
        if (! isset($this->store[$id])) {
            return false;
        }
        unset($this->store[$id]);
        return true;
    }

    public function listProfiles(): array
    {
        return $this->store;
    }
}
