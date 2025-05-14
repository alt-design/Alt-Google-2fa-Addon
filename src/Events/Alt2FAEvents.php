<?php namespace AltDesign\AltGoogle2FA\Events;

use AltDesign\AltSeo\Helpers\Data;
use Statamic\Events;
use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;

/**
 * Class Alt2FAEvents
 *
 * @package  AltDesign\AltGoogle2FA
 * @author   Benammi Swift <benammi@alt-design.net>, Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class Alt2FAEvents
{
    /**
     * Sets the events to listen for
     *
     * @var string[]
     */
    protected $events = [
        Events\UserBlueprintFound::class => 'addSeoData',
    ];

    /**
     * Subscribe to events
     *
     * @param $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(Events\UserBlueprintFound::class, self::class.'@'.'addUserFields');
    }

    public function addUserFields(&$event)
    {
        $oldDirectory = Blueprint::directory();
        // conditionally load the published blueprint
        $path = base_path('resources/blueprints/vendor/alt-google-2fa/2fa-user-fields.yaml');

        if (!file_exists($path)) {
            $path = __DIR__.'/../../resources/blueprints/2fa-user-fields.yaml';
        }

        $blueprint = YAML::file($path)->parse();
        $blueprintReady = $event->blueprint->contents();

        $blueprintReady['tabs'] = array_merge(
            $blueprintReady['tabs']?? [],
            $blueprint['tabs']?? []
        );
        $event->blueprint->setContents($blueprintReady);

        Blueprint::setDirectory($oldDirectory);
        return $event;
    }
}
