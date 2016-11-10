<?php
namespace Lamansky\Hash\Doctrine;

/**
 * @package Hash\Doctrine
 */
class Hash384Type extends HashType {
    protected function getBitLength() : int { return 384; }
}
