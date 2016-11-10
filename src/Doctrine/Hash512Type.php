<?php
namespace Lamansky\Hash\Doctrine;

/**
 * @package Hash\Doctrine
 */
class Hash512Type extends HashType {
    protected function getBitLength() : int { return 512; }
}
