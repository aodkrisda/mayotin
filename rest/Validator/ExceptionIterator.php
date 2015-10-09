<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

use RecursiveArrayIterator;
use Respect\Validation\Exceptions\AbstractNestedException;

class ExceptionIterator extends RecursiveArrayIterator
{
    protected $fullRelated;

    public function __construct($target, $fullRelated = false)
    {
        $this->fullRelated = $fullRelated;
        parent::__construct(is_array($target) ? $target : array($target));
    }

    public function hasChildren()
    {
        if (!$this->current() instanceof AbstractNestedException) {
            return false;
        } else {
            return (boolean) $this->current()->getRelated($this->fullRelated);
        }
    }

    public function getChildren()
    {
        return new static($this->current()->getRelated($this->fullRelated), $this->fullRelated);
    }
}
