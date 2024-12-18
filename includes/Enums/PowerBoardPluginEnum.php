<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\Repositories\LogRepository;

final class PowerBoardPluginEnum extends AbstractEnum {
	public const REPOSITORIES = [
		LogRepository::class,
	];
}