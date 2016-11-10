<?php
namespace Lamansky\Hash;

/**
 * Represents a hash of any length (256-bit, 384-bit, 512-bit, etc.)
 *
 * @package Hash
 */
class Hash {
	protected $bit_length;
	protected $hash;

	/**
	 * Creates a random hash of the specified bit length.
	 *
	 * @param int $bit_length The number of bits in the hash, e.g. 256, 384, or 512.
	 * @return Hash
	 *
	 * @throws DomainException if the `$bit_length` is not divisible by 8.
	 */
	public static function generateRandom(int $bit_length) : Hash {
		if ($bit_length % 8 !== 0)
			throw new \DomainException();

		return new static($bit_length, openssl_random_pseudo_bytes($bit_length / 8));
	}

	/**
	 * Creates a hash of a given string using the specified algorithm.
	 *
	 * @see http://php.net/manual/en/function.hash-algos.php for more info on available algorithms.
	 *
	 * @param string $algorithm A string which identifies the method to be used
	 *                          in turning the data into a hash, e.g. `sha256`.
	 * @param string $data The string to be turned into a hash using the algorithm.
	 * @return Hash
	 *
	 * @throws DomainException if PHP does not support the requested algorithm.
	 */
	public static function generate(string $algorithm, string $data) : Hash {
		if (!in_array($algorithm, hash_algos())) throw new \DomainException();
		$hash = hash($algorithm, $data, true);
		return new static(strlen($hash)*8, $hash);
	}

	/**
	 * Creates a `Hash` object as a wrapper around an existing hash string.
	 *
	 * @param int $bit_length The number of bits in the hash, e.g. 256, 384, or 512.
	 * @param string $hash The hash as either a binary string or a hexadecimal string.
	 *
	 * @throws DomainException if the `$bit_length` is not divisible by 8.
	 * @throws InvalidArgumentException if the hash is the length of a hexadecimal
	 *                                  string but is not actually a valid hexadecimal string.
	 * @throws LengthException if the hash does not actually have the number of
	 *                         bits specified in the `$bit_length`.
	 */
	public function __construct(int $bit_length, string $hash) {
		if ($bit_length % 8 !== 0)
			throw new \DomainException();

		//Is it the length of a hexadecimal string? If so, convert it.
		if (strlen($hash) === $bit_length / 4) {
			//Is the hexadecimal string valid?
			if (!ctype_xdigit($hash))
				throw new \InvalidArgumentException();

			//Convert hexadecimal to binary
			$hash = hex2bin($hash);
		}

		//Is it the length of a binary string? If not, throw exception.
		if (strlen($hash) !== $bit_length / 8)
			throw new \LengthException();

		$this->bit_length = $bit_length;
		$this->hash = $hash;
	}

	/**
	 * When the `Hash` is cast to a string, return the binary hash.
	 */
	public function __toString() {
		return $this->hash;
	}

	/**
	 * Returns the hash as a hexadecimal string.
	 *
	 * @return string
	 */
	public function toHex() : string {
		return bin2hex($this->hash);
	}

	/**
	 * Performs a timing-safe comparison to determine whether another hash
	 * is equal to this one.
	 *
	 * @uses hash_equals to protect against timing attacks.
	 * @param Hash|string $other
	 * @return bool True if the hashes are identical. False if the hashes are
	 *              different or if the other hash is invalid.
	 */
	public function equals($other) : bool {

		if (!($other instanceof static)) {
			try {
				$other = new static($this->bit_length, $other);
			} catch (\Exception $e) {
				return false;
			}
		}

		return hash_equals((string)$this, (string)$other);
	}
}
