<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\SettingGroups;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Services\SettingsService;

class AdvancedSettingService extends AbstractSettingService {
  public function init_form_fields(): void {
    $debuggingOptionKey = SettingsService::getInstance()
     ->getOptionName( $this->id, [
       SettingGroups::ADVANCED()->name,
       'Debugging'
     ] );

    $form_fields = $this->get_form_fields();
    $this->form_fields[ $debuggingOptionKey ] = [
      'type' => 'checkbox',
      'label' => 'Enable testing mode (Internal use only)',
      'title' => 'Debugging',
    ];
  }
	protected function getId(): string {
		return SettingsTabs::ADVANCED()->value;
	}
}
