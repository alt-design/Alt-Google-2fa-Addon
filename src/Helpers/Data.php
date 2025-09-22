<?php namespace AltDesign\AltGoogle2FA\Helpers;

use Statamic\Fields\BlueprintRepository;
use Statamic\Facades\YAML;
use Statamic\Filesystem\Manager;


class Data
{

    protected string $type = 'settings';

    protected $currentFile;

    protected array $data;

    public function __construct(protected Manager $manager)
    {

        $this->currentFile = $this->manager->disk()->get('content/alt-google-2fa/' . $this->type . '.yaml');

        $this->data = Yaml::parse($this->currentFile);
    }

    public function superUserPolicy(): string
    {
        return $this->data['alt_google_2fa_forced_super_user'] ?? 'off';
    }

    public function forcedRoles(): array
    {
        return $this->data['alt_google_2fa_forced_roles'] ?? [];
    }

    public function optionalRoles(): array
    {
        return $this->data['alt_google_2fa_optional_roles'] ?? [];
    }

    public function noRedirectUnverified(): bool
    {
        return $this->data['alt_google_2fa_unverified_user_no_redirect'] ?? false;
    }

    public function getBlueprint($default = false)
    {
        if($default) {
            return with(new BlueprintRepository)->setDirectory(__DIR__ . '/../../resources/blueprints')->find($this->type);
        }

        return false;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function setAll(array $data): void
    {
        $this->data = $data;

        $this->manager->disk()->put('content/alt-google-2fa/' . $this->type . '.yaml', Yaml::dump($this->data));
    }

}
