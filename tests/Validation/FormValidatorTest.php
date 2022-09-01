<?php

namespace Validation;

use Easycode\Encryption\Hash;
use Easycode\Validation\FormValidator;
use PHPUnit\Framework\TestCase;

class FormValidatorTest extends TestCase
{

    public function testGetRules()
    {
        $rules = [
            'firstName' => [
                'label' => 'First Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 75
            ],
            'lastName' => [
                'label' => 'Last Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 50
            ],
            'username' => [
                'label' => 'Username',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 25
            ],
            'email' => [
                'label' => 'E-mail',
                'required' => true,
                'email' => true
            ],
            'password' => [
                'label' => 'Password',
                'required' => true,
                'minlength' => 8
            ],
            'birthday' => [
                'label' => 'Birthday',
                'required' => true
            ],
            'phone' => [
                'label' => 'Phone',
                'required' => true
            ],
            'website' => [
                'label' => 'Web Site',
                'required' => true,
                'url' => true
            ],
            'ip' => [
                'label' => 'IP',
                'required' => true,
                'ipv4' => true
            ],
            'term' => [
                'label' => 'Terms',
                'required' => true,
                'bool' => true
            ],
        ];
        $data = [
            'firstName' => 'Lucas',
            'lastName' => 'Alves',
            'username' => 'Codestep',
            'email' => 'codestep@codingstep.com.br',
            'password' => Hash::encryptPassword('code'),
            'birthday' => '03/08/1998',
            'phone' => '61999829395',
            'website' => 'https://www.codeingstep.com.br',
            'ip' => '10.10.5.5',
            'term' => 'on'
        ];

        $formValidator = new FormValidator($data, $rules);

        $this->assertEquals($rules, $formValidator->getRules(), 'The rules are not the same.');
    }

    public function testGetData()
    {
        $rules = [
            'firstName' => [
                'label' => 'First Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 75
            ],
            'lastName' => [
                'label' => 'Last Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 50
            ],
            'username' => [
                'label' => 'Username',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 25
            ],
            'email' => [
                'label' => 'E-mail',
                'required' => true,
                'email' => true
            ],
            'password' => [
                'label' => 'Password',
                'required' => true,
                'minlength' => 8
            ],
            'birthday' => [
                'label' => 'Birthday',
                'required' => true
            ],
            'phone' => [
                'label' => 'Phone',
                'required' => true
            ],
            'website' => [
                'label' => 'Web Site',
                'required' => true,
                'url' => true
            ],
            'ip' => [
                'label' => 'IP',
                'required' => true,
                'ipv4' => true
            ],
            'term' => [
                'label' => 'Terms',
                'required' => true,
                'bool' => true
            ],
        ];
        $data = [
            'firstName' => 'Lucas',
            'lastName' => 'Alves',
            'username' => 'Codestep',
            'email' => 'codestep@codingstep.com.br',
            'password' => Hash::encryptPassword('code'),
            'birthday' => '03/08/1998',
            'phone' => '61999829395',
            'website' => 'https://www.codeingstep.com.br',
            'ip' => '10.10.5.5',
            'term' => 'on'
        ];

        $formValidator = new FormValidator($data, $rules);

        $this->assertEquals($data, $formValidator->getData(), 'The data are not the same.');
    }

    public function testGetErrors()
    {
        $rules = [
            'firstName' => [
                'label' => 'First Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 75
            ],
            'lastName' => [
                'label' => 'Last Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 50
            ],
            'username' => [
                'label' => 'Username',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 25
            ],
            'email' => [
                'label' => 'E-mail',
                'required' => true,
                'email' => true
            ],
            'password' => [
                'label' => 'Password',
                'required' => true,
                'minlength' => 8
            ],
            'birthday' => [
                'label' => 'Birthday',
                'required' => true
            ],
            'phone' => [
                'label' => 'Phone',
                'required' => true
            ],
            'website' => [
                'label' => 'Web Site',
                'required' => true,
                'url' => true
            ],
            'ip' => [
                'label' => 'IP',
                'required' => true,
                'ipv4' => true
            ],
            'term' => [
                'label' => 'Terms',
                'required' => true,
                'bool' => true
            ],
        ];
        $data = [
            'firstName' => 'Lucas',
            'lastName' => 'Alves',
            'username' => 'Codestep',
            'email' => 'codestep@codingstep.com.br',
            'password' => Hash::encryptPassword('code'),
            'birthday' => '03/08/1998',
            'phone' => '61999829395',
            'website' => 'https://www.codeingstep.com.br',
            'ip' => '10.10.5.5',
            'term' => 'on'
        ];

        $formValidator = new FormValidator($data, $rules);

        $this->assertIsArray($formValidator->getErrors(), 'Could not collect validation errors.');
    }

    public function testValidate()
    {
        $rules = [
            'firstName' => [
                'label' => 'First Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 75
            ],
            'lastName' => [
                'label' => 'Last Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 50
            ],
            'username' => [
                'label' => 'Username',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 25
            ],
            'email' => [
                'label' => 'E-mail',
                'required' => true,
                'email' => true
            ],
            'password' => [
                'label' => 'Password',
                'required' => true,
                'minlength' => 8
            ],
            'birthday' => [
                'label' => 'Birthday',
                'required' => true
            ],
            'phone' => [
                'label' => 'Phone',
                'required' => true
            ],
            'website' => [
                'label' => 'Web Site',
                'required' => true,
                'url' => true
            ],
            'ip' => [
                'label' => 'IP',
                'required' => true,
                'ipv4' => true
            ],
            'term' => [
                'label' => 'Terms',
                'required' => true,
                'bool' => true
            ],
        ];
        $data = [
            'firstName' => 'Lucas',
            'lastName' => 'Alves',
            'username' => 'Codestep',
            'email' => 'codestep@codingstep.com.br',
            'password' => Hash::encryptPassword('code'),
            'birthday' => '03/08/1998',
            'phone' => '61999829395',
            'website' => 'https://www.codeingstep.com.br',
            'ip' => '10.10.5.5',
            'term' => 'on'
        ];

        $formValidator = new FormValidator($data, $rules);

        $this->assertTrue($formValidator->validate(), 'An error was found in the tested data.');

        print_r($formValidator->getErrors());
    }

    public function testSetData()
    {
        $data = [
            'firstName' => 'Lucas',
            'lastName' => 'Alves',
            'username' => 'Codestep',
            'email' => 'codestep@codingstep.com.br',
            'password' => Hash::encryptPassword('code'),
            'birthday' => '03/08/1998',
            'phone' => '61999829395',
            'website' => 'https://www.codeingstep.com.br',
            'ip' => '10.10.5.5',
            'term' => 'on'
        ];

        $formValidator = new FormValidator();
        $formValidator->setData($data);

        $this->assertEquals($data, $formValidator->getData(), 'The data are not the same.');
    }

    public function testSetRules()
    {
        $rules = [
            'firstName' => [
                'label' => 'First Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 75
            ],
            'lastName' => [
                'label' => 'Last Name',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 50
            ],
            'username' => [
                'label' => 'Username',
                'required' => true,
                'alphabetical' => true,
                'minlength' => 3,
                'maxlength' => 25
            ],
            'email' => [
                'label' => 'E-mail',
                'required' => true,
                'email' => true
            ],
            'password' => [
                'label' => 'Password',
                'required' => true,
                'minlength' => 8
            ],
            'birthday' => [
                'label' => 'Birthday',
                'required' => true
            ],
            'phone' => [
                'label' => 'Phone',
                'required' => true
            ],
            'website' => [
                'label' => 'Web Site',
                'required' => true,
                'url' => true
            ],
            'ip' => [
                'label' => 'IP',
                'required' => true,
                'ipv4' => true
            ],
            'term' => [
                'label' => 'Terms',
                'required' => true,
                'bool' => true
            ],
        ];

        $formValidator = new FormValidator();
        $formValidator->setRules($rules);

        $this->assertEquals($rules, $formValidator->getRules(), 'The rules are not the same.');
    }
}
