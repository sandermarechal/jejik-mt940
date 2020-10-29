<?php

declare(strict_types=1);

/*
 * This file is part of the Powercloud\MT940 (a Fork of: Jejik\MT940) library
 *
 * Copyright (c) 2020 Powercloud GmbH <d.richter@powercloud.de>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Powercloud\MT940\Exception;

/**
 * Class UserException
 * @package Powercloud\MT940\Exception
 */
class UserException extends \Exception
{
    /**
     * UserException constructor.
     * Creates the exception instance with a hardcoded message.
     */
    public function __construct()
    {
        parent::__construct('UserException');
    }
}
