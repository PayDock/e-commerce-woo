<?php

namespace PowerBoard;

use PowerBoard\Abstract\AbstractSingleton;
use PowerBoard\Hooks\ActivationHook;
use PowerBoard\Hooks\DeactivationHook;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\ActionsService;
use PowerBoard\Services\Assets\AdminAssetsService;
use PowerBoard\Services\FiltersService;
use PowerBoard\Services\Settings\LiveConnectionSettingService;

if (!class_exists('\PowerBoard\PowerBoardPlugin')) {
    final class PowerBoardPlugin extends AbstractSingleton
    {
        public const REPOSITORIES = [
            LogRepository::class,
        ];

        public const PLUGIN_PREFIX = 'power_board';

        public const VERSION = '1.0.0';

        protected static ?PowerBoardPlugin $instance = null;

        protected LiveConnectionSettingService|null $paymentService = null;

        protected function __construct()
        {
            register_activation_hook(POWER_BOARD_PLUGIN_FILE, [ActivationHook::class, 'handle']);
            register_deactivation_hook(POWER_BOARD_PLUGIN_FILE, [DeactivationHook::class, 'handle']);

            ActionsService::getInstance();
            FiltersService::getInstance();


        }
    }
}
