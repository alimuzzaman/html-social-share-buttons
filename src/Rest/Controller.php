<?php
namespace HtmlSocialShare\Rest;

use HtmlSocialShare\ProfileManagerInterface;

class Controller
{
    protected $profiles;

    public function __construct(ProfileManagerInterface $profiles)
    {
        $this->profiles = $profiles;
    }

    public function listProfiles()
    {
        return [ 'status' => 200, 'body' => $this->profiles->listProfiles() ];
    }

    public function createProfile($payload)
    {
        if (!is_array($payload)) {
            return ['status' => 400, 'body' => 'Invalid payload'];
        }

    $id = $this->profiles->createProfile($payload);
        return ['status' => 201, 'body' => ['id' => $id]];
    }
}
