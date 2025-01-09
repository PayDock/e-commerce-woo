<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class NotificationEventsEnum extends AbstractEnum {
	public const TRANSACTION_SUCCESS = 'Transaction Success';
	public const TRANSACTION_FAILURE = 'Transaction Failure';
	public const REFUND_SUCCESS      = 'Refund Successful';
}
