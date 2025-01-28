<?php
declare( strict_types=1 );

namespace PowerBoard\Enums;

class APIActionEnum {
	public const REFUND                         = 'Refund';
	public const CREATE_INTENT                  = 'Create intent';
	public const GET_CONFIGURATION_TEMPLATE_IDS = 'Get Configuration Template IDs';
	public const GET_CUSTOMISATION_TEMPLATE_IDS = 'Get Customisation Templates IDs';
	public const CREATE_TOKEN                   = 'Create token';
}
