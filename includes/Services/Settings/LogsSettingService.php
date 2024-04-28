<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Repositories\LogRepository;

class LogsSettingService extends AbstractSettingService {
	public function generate_settings_html( $form_fields = [], $echo = true ): ?string {
		$page = ! empty( $_GET['page_number'] ) ? sanitize_text_field( $_GET['page_number'] ) : 1;
		$perPage = ! empty( $_GET['per_page'] ) ? sanitize_text_field( $_GET['per_page'] ) : 50;
		$order = ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc';
		$orderBy = ! empty( $_GET['orderBy'] ) ? sanitize_text_field( $_GET['orderBy'] ) : 'created_at';

		$tabs = $this->getTabs();
		$records = ( new LogRepository() )->getLogs( $page, $perPage, $orderBy, $order );

		if ( $echo ) {
			$this->templateService->includeAdminHtml( 'admin', compact( 'tabs', 'records' ) );
		} else {
			return $this->templateService->getAdminHtml( 'admin', compact( 'tabs', 'records' ) );
		}

		return null;
	}

	protected function getId(): string {
		return SettingsTabs::LOG()->value;
	}
}
