<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class ChargeStatuses extends AbstractEnum {
	protected const COMPLETE = 'Complete';
	protected const PENDING = 'Pending';
	protected const REQUESTED = 'Requested';
	protected const FAILED = 'Failed';
	protected const REFUND_REQUESTED = 'refund_requested';
	protected const REFUNDED = 'refunded';
	protected const PRE_AUTHENTICATION_PENDING = 'Pre_authentication_pending';
	protected const DECLINED = 'declined';
	protected const CANCELLED = 'cancelled';
}
