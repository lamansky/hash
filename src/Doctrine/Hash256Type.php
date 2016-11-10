<?php
namespace Lamansky\Hash\Doctrine;

/**
 * @package Hash\Doctrine
 */
class Hash256Type extends HashType {
    protected function getBitLength() : int { return 256; }
}
