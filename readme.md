# Hash 1.0.0
Author: John Lamansky  
Release Date: 2016-11-10

A simple library for comparing hashes and generating tokens. Includes Doctrine types for efficient database storage and retrieval. Requires PHP 7.

## Installation

Use [Composer](http://getcomposer.org):

```bash
composer require lamansky/hash
```

## Basic Usage
```php
<?php
use Lamansky\Hash\Hash;

//Hash a string
echo Hash::generate('md5', 'test')->toHex(); //098f6bcd4621d373cade4e832627b4f6

//Generate random 128-bit and 256-bit tokens
$a = Hash::generateRandom(128);
$b = Hash::generateRandom(256);

//Timing-safe comparison
var_dump($a->equals($b)); //bool(false)

//Convert Hash to hex string
$hex = $a->toHex();
echo $hex; //4df3fa5a70eaa4097740b46017d428e2

//Compare Hash with string
var_dump($a->equals($hex)); //bool(true)

//Turn string back into a Hash
$c = new Hash(128, $hex);

var_dump($c->equals($a)); //bool(true)
```

## Doctrine Usage
```php
<?php
use Doctrine\DBAL\Types\Type;
Type::addType('hash256', 'Lamansky\Hash\Doctrine\Hash256Type'); //256-bit hash
Type::addType('hash384', 'Lamansky\Hash\Doctrine\Hash384Type'); //384-bit hash
Type::addType('hash512', 'Lamansky\Hash\Doctrine\Hash512Type'); //512-bit hash
```

```php
<?php
use Lamansky\Hash\Hash;

/**
 * @Entity
 */
class Session {

    /**
    * @Column(type="hash256")
    */
    protected $token;

    public function __construct() {
        $this->token = Hash::generateRandom(256);
    }

    public function tokenEquals($other) : bool {
        return $this->token->equals($other);
    }
}
```

## Changelog

### 1.0.0 (2016-11-10)
* Initial release
