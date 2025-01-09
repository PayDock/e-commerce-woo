<?php

namespace PowerBoard\Enums;

class ChargeStatusesEnum {
	public const COMPLETE                   = 'Complete';
	public const PENDING                    = 'Pending';
	public const REQUESTED                  = 'Requested';
	public const FAILED                     = 'Failed';
	public const REFUND_REQUESTED           = 'refund_requested';
	public const REFUNDED                   = 'refunded';
	public const PRE_AUTHENTICATION_PENDING = 'Pre_authentication_pending';
	public const DECLINED                   = 'declined';
	public const CANCELLED                  = 'cancelled';
}
