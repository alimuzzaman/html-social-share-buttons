<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

/**
 * A WordPress-facing service whose only side effect is registering hooks.
 */
interface HookSubscriber {
	public function registerHooks();
}
