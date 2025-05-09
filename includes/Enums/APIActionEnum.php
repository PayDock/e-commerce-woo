<?php
declare( strict_types=1 );

namespace WooPlugin\Enums;

class APIActionEnum {
	public const REFUND                                     = 'Refund';
	public const CREATE_INTENT                              = 'Create intent';
	public const VALIDATE_INTENT_STATUS                     = 'Validate intent status';
	public const GET_CONFIGURATION_TEMPLATE_IDS             = 'Get Configuration Template IDs';
	public const GET_CUSTOMISATION_TEMPLATE_IDS             = 'Get Customisation Template IDs';
	public const GET_CONFIGURATION_TEMPLATES_FOR_VALIDATION = 'Get Configuration Template IDs: Token Validation';
}
