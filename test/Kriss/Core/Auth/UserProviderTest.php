<?php 

use Kriss\Core\Auth\UserProvider as UserProvider;

class UserProviderTest extends \PHPUnit\Framework\TestCase {
    public function testUserProvider() {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $criteria = ['username' => 'username'];
        $user = 'user';
        $model->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($user));
        $userProvider = new UserProvider($model);
        $this->assertSame($user, $userProvider->loadUser($criteria));
    }
}