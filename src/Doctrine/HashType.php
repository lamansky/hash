<?php
namespace Lamansky\Hash\Doctrine;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Lamansky\Hash\Hash;

/**
 * Allows Doctrine ORM to store `Hash` objects in the database and retrieve them.
 *
 * The class needs to be extended with information on how long the hash is.
 * You can use the built-in `Hash256Type`, `Hash384Type`, or `Hash512Type`,
 * or create your own.
 *
 * @package Hash\Doctrine
 */
abstract class HashType extends Type {

    abstract protected function getBitLength() : int;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) : string {
        $fieldDeclaration['length'] = $this->getBitLength() / 8;
		$fieldDeclaration['fixed'] = true;
        return $platform->getBinaryTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if (!$value)
            return null;

        if ($value instanceof Hash)
            return $value;

        try {
            $hash = new Hash($this->getBitLength(), $value);
        } catch (\Exception $e) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $hash;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value) return null;
        return (string)$value;
    }

    public function getName() {
        return 'hash' . $this->getBitLength();
    }

    /**
     * Since we're reusing the binary SQL type, we need a SQL comment so
     * Doctrine can distinguish the `hash256` type from the `binary` type.
     *
     * @param AbstractPlatform $platform
     * @return true
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform) {
        return true;
    }
}
