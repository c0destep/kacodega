<?php

namespace Encryption;

use Easycode\Encryption\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{

    public function testVerifyPassword()
    {
        $encryptedPassword = Hash::encryptPassword('easycode');
        $this->assertNotEmpty($encryptedPassword, 'Entered password is empty');

        $isValid = Hash::verifyPassword('easycode', $encryptedPassword);
        $this->assertTrue($isValid, 'This password is not valid');
    }

    public function testEncryptPassword()
    {
        $encryptedPassword = Hash::encryptPassword('easycode');
        $this->assertNotEmpty($encryptedPassword, 'Entered password is empty');
    }
}
