<?php

use Kriss\Core\Form\Form as Form;

class FormTest extends \PHPUnit\Framework\TestCase {
    function testDefaultForm()
    {
        $form = new Form();
        $form = $form->getForm();
        $this->assertSame('', $form['*']['action']);
        $this->assertSame('GET', $form['*']['method']);
    }

    function testAction()
    {
        $form = new Form();
        $form->setAction('foo');
        $this->assertSame('foo', $form->getForm()['*']['action']);
        $this->assertSame('foo', $form->getAction());
    }

    function testMethod()
    {
        $form = new Form();
        $form->setMethod('GET');
        $this->assertSame('GET', $form->getForm()['*']['method']);
        $this->assertSame('GET', $form->getMethod());
        $form->setMethod('POST');
        $this->assertSame('POST', $form->getForm()['*']['method']);
        $form->setMethod('PUT');
        $this->assertSame('POST', $form->getForm()['*']['method']);
        $this->assertSame('PUT', $form->getForm()['_method']['value']);
        $this->assertSame('PUT', $form->getMethod());
    }

    function testArrayData()
    {
        $form = new Form();
        $data = [
            'username' => 'user',
            'password' => 'pass',
            'display' => 'none',
        ];

        $form->setData($data);
        $this->assertSame($data, $form->getData());
        $form->setRule('password', [
            'type' => 'password',
            'value' => '',
        ]);
        $form->setRule('display', null);
        $form->setRule('foo', ['bar']);

        $form = $form->getForm();
        $this->assertSame($data['username'], $form['username']['value']);
        $this->assertSame('text', $form['username']['type']);
        $this->assertSame('', $form['password']['value']);
        $this->assertSame('password', $form['password']['type']);
        $this->assertSame(['bar'], $form['foo']);
        $this->assertFalse(array_key_exists('display', $form));
    }


    function testObjectData()
    {
        $form = new Form();
        $data = new stdClass;
        $data->username = 'user';
        $data->password = 'pass';
        $data->display = 'none';

        $form->setData($data);
        $this->assertSame($data, $form->getData());
        $passwordRule = [
            'type' => 'password',
            'value' => '',
        ];
        $form->setRule('password', $passwordRule);
        $form->setRule('display', null);

        $this->assertSame($data->username, $form->getForm()['username']['value']);
        $this->assertSame('text', $form->getForm()['username']['type']);
        $this->assertSame('', $form->getForm()['password']['value']);
        $this->assertSame('password', $form->getForm()['password']['type']);
        $this->assertFalse(array_key_exists('display', $form));
        $this->assertSame([], $form->getRule('foo'));
        $this->assertSame($passwordRule, $form->getRule('password'));
    }

    function testArrayFormatter()
    {
        $form = new Form();
        $data = [
            'username' => 'user',
            'password' => 'pass',
            'display' => 'none',
        ];

        $form->setData($data);
        $form->setFormatter('password', function($value){return 'hashPass';});

        $form->setFormData(['password' => 'toto']);
        $formData = $form->getData();
        $this->assertSame('hashPass', $formData['password']);
    }

    function testObjectFormatter()
    {
        $form = new Form();
        $data = new stdClass;
        $data->username = 'user';
        $data->password = 'pass';

        $form->setData($data);
        $form->setFormatter('password', function($value){return 'hashPass';});

        $form->setFormData(['password' => 'toto']);
        $this->assertSame('hashPass', $form->getData()->password);
    }

    function testSubFormObject()
    {
        $address = new stdClass;
        $address->city = 'city';
        $address->zipCode = 'code';

        $person = new stdClass;
        $person->firstname = 'first';
        $person->lastname = 'last';
        $person->address = $address;
        $person->phone = ['first' => '123', 'second' => '456'];
        
        $addressForm = new Form($address);
        $personForm = new Form($person);
        $personForm->setRule('address', $addressForm);

        $form = $personForm->getForm();
        $this->assertSame('code', $form['address[zipCode]']['value']);
        $personForm->setFormData(['firstname' => 'firstname', 'address[zipCode]' => 'zip', 'phone[first]' => '321']);
        $person = $personForm->getData();
        
        $this->assertSame('firstname', $person->firstname);
        $this->assertSame('zip', $address->zipCode);
        $this->assertSame('321', $person->phone['first']);
        $this->assertSame('456', $person->phone['second']);
    }

    function testSubFormArray()
    {
        $address = new stdClass;
        $address->city = 'city';
        $address->zipCode = 'code';

        $person = [
            'firstname' => 'first',
            'lastname' => 'last',
            'address' => $address,
            'phone' => ['first' => '123', 'second' => '456']
        ];
        
        $addressForm = new Form($address);
        $personForm = new Form($person);
        $personForm->setRule('address', $addressForm);

        $form = $personForm->getForm();
        $this->assertSame('code', $form['address[zipCode]']['value']);
        $personForm->setFormData(['firstname' => 'firstname', 'address[zipCode]' => 'zip', 'phone[first]' => '321']);
        $person = $personForm->getData();
        
        $this->assertSame('firstname', $person['firstname']);
        $this->assertSame('zip', $address->zipCode);
        $this->assertSame('321', $person['phone']['first']);
        $this->assertSame('456', $person['phone']['second']);
    }

    function testMultipleValueForm()
    {
        $address1 = new stdClass;
        $address1->city = 'city1';
        $address1->zipCode = 'code1';

        $address2 = new stdClass;
        $address2->city = 'city2';
        $address2->zipCode = 'code2';

        $address = [$address1, $address2];
        
        $addressForm = new Form($address);

        $form = $addressForm->getForm();        
        $this->assertSame($form['_[0][city]']['value'], $address1->city);
        $form = $addressForm->getForm('address');
        $this->assertSame($form['address[0][city]']['value'], $address1->city);
        
        $addressForm->setFormData(['address[0][city]' => 'city']);
        $address = $addressForm->getData('address');
        $this->assertSame($address[0]->city, 'city');
        $addressForm->setFormData(['_[1][city]' => 'city']);
        $address = $addressForm->getData();
        $this->assertSame($address[1]->city, 'city');
    }

    function testSubFormsArray()
    {
        $address1 = new stdClass;
        $address1->city = 'city1';
        $address1->zipCode = 'code1';

        $address2 = new stdClass;
        $address2->city = 'city2';
        $address2->zipCode = 'code2';

        $address = [$address1, $address2];
        
        $addressForm = new Form($address);

        $person = [
            'firstname' => 'first',
            'lastname' => 'last',
            'address' => $address,
            'phone' => ['first' => '123', 'second' => '456']
        ];
        
        $personForm = new Form($person);
        $personForm->setRule('address', $addressForm);

        $form = $personForm->getForm();

        $this->assertSame($form['address[1][city]']['value'], 'city2');
    }

    function testCanNotModifyId()
    {
        $address = new stdClass;
        $address->id = 1;
        $address->foo = 'foo';

        $addressForm = new Form($address);
        $addressForm->setFormData(['id' => '2', 'foo' => 'bar']);
        $address = $addressForm->getData();
        
        $this->assertSame('bar', $address->foo);
        $this->assertSame(1, $address->id);
    }
}
