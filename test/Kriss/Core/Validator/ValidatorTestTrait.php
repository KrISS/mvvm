<?php
trait ValidatorTestTrait {
    private $invalidArrayData;
    private $invalidObjectData;
    private $validArrayData;
    private $validObjectData;

    public function setUp()
    {
        $this->validArrayData = [
            'email' => 'test@test.fr',
            'inArray' => '2',
            'required' => true,
            'minLength' => '123456',
        ];
        $this->invalidArrayData = [
            'email' => 'testtest.fr',
            'inArray' => '4',
            'required' => null,
            'minLength' => '123',
        ];
        $this->validObjectData = new stdClass;
        foreach($this->validArrayData as $key => $value) $this->validObjectData->$key = $value;
        $this->invalidObjectData = new stdClass;
        foreach($this->invalidArrayData as $key => $value) $this->invalidObjectData->$key = $value;
    }

    private function checkValidator($validator)
    {
        $this->assertSame(true, $validator->isValid($this->validArrayData));
        $this->assertSame(true, $validator->isValid($this->validObjectData));
        $this->assertSame(false, $validator->isValid($this->invalidArrayData));
        $this->assertSame(false, $validator->isValid($this->invalidObjectData));
    }
    
    public function testDefaultValidator()
    {
        $validator = $this->getValidator();
        $this->assertSame(true, $validator->isValid(null));
    }
    
    public function testEmailValidator()
    {
        $validator = $this->getValidator();
        $validator->setConstraints('email', [['email']]);
        $this->checkValidator($validator);
    }

    public function testInArrayValidator()
    {
        $validator = $this->getValidator();
        $validator->setConstraints('inArray', [['inArray', [['1', '2', '3']]]]);
        $this->checkValidator($validator);
    }

    public function testRequiredValidator()
    {
        $validator = $this->getValidator();
        $validator->setConstraints('required', [['required']]);
        $this->checkValidator($validator);
    }

    public function testMinLengthValidator()
    {
        $validator = $this->getValidator();
        $validator->setConstraints('minLength', [['minLength', [4]]]);
        $this->checkValidator($validator);
    }

    public function testUnknownConstraintValidator()
    {
        $validator = $this->getValidator();
        $this->expectException('\Exception');
        $validator->setConstraints('unknown', [['unknown']]);
        $validator->isValid(null);
    }

    public function testGetErrorsValidator()
    {
        $validator = $this->getValidator();
        $validator->setConstraints('email', [['email']]);
        $validator->setConstraints('required', [['required']]);
        $validator->setConstraints('minLength', [['minLength', [4]]]);
        $validator->isValid($this->validArrayData);
        $this->assertSame(0, count($validator->getErrors()));
        $validator->isValid($this->invalidArrayData);
        $this->assertSame(3, count($validator->getErrors()));
    }

    public function testGetConstraintsValidator()
    {
        $validator = $this->getValidator();
        $this->assertSame(0, count($validator->getConstraints('test')));
        $validator->setConstraints('test', [['email']]);
        $this->assertSame(1, count($validator->getConstraints('test')));
        $validator->setConstraints('test', [['required']]);
        $this->assertSame(1, count($validator->getConstraints('test')));
        $validator->setConstraints('test', [['email'], ['required']]);
        $this->assertSame(2, count($validator->getConstraints('test')));
    }
}