<?php

namespace Paydock;

use Paydock\Abstract\AbstractSingleton;
use Paydock\Hooks\ActivationHook;
use Paydock\Hooks\DeactivationHook;
use Paydock\Repositories\LogRepository;
use Paydock\Services\ActionsService;
use Paydock\Services\Assets\AdminAssetsService;
use Paydock\Services\FiltersService;
use Paydock\Services\Settings\LiveConnectionSettingService;

if (!class_exists('\Paydock\PaydockPlugin')) {
    final class PaydockPlugin extends AbstractSingleton
    {
        public const REPOSITORIES = [
            LogRepository::class,
        ];

        public const PLUGIN_PREFIX = 'pay_dock';

        public const VERSION = '1.0.0';

        protected static ?PaydockPlugin $instance = null;

        protected LiveConnectionSettingService|null $paymentService = null;

        protected function __construct()
        {
            register_activation_hook(PAY_DOCK_PLUGIN_FILE, [ActivationHook::class, 'handle']);
            register_deactivation_hook(PAY_DOCK_PLUGIN_FILE, [DeactivationHook::class, 'handle']);

            ActionsService::getInstance();
            FiltersService::getInstance();


        }
    }
}

