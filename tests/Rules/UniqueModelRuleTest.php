<?php

namespace Chubbyphp\Tests\Validation\Rules;

use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Respect\Validation\Rules\RuleTestCase;

/**
 * @covers Chubbyphp\Validation\Rules\UniqueModelRule
 */
final class UniqueModelRuleTest extends RuleTestCase
{
    public function testValidate()
    {
        $user = $this->getUser('id1', 'firstname.lastname@domain.tld');

        $uniqueModel = new UniqueModelRule(['email']);
        $uniqueModel->setRepository($this->getUserRepository([
            [
                'arguments' => [['email' => 'firstname.lastname@domain.tld']],
                'return' => null,
            ],
        ]));

        $uniqueModel->validate($user);
    }

    public function testValidateWithInvalidInput()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage(
            'The to validate value needs to be an instance of '.ValidatableModelInterface::class
            .', stdClass given!'
        );

        $uniqueModel = new UniqueModelRule(['email']);

        $uniqueModel->validate(new \stdClass());
    }

    public function testValidateWithoutRepositoryExceptException()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(
            'Rule '.UniqueModelRule::class.' needs a repository of interface '.RepositoryInterface::class
            .', please call setRepository before validate.'
        );

        $user = $this->getUser('id1', 'firstname.lastname@domain.tld');

        $uniqueModel = new UniqueModelRule(['email']);

        $uniqueModel->validate($user);
    }

    public function providerForInvalidInput()
    {
        $user = $this->getUser('id1', 'firstname.lastname@domain.tld');

        $uniqueModel = new UniqueModelRule(['email']);
        $uniqueModel->setRepository($this->getUserRepository([
            [
                'arguments' => [['email' => 'firstname.lastname@domain.tld']],
                'return' => $this->getUser('id2', 'firstname.lastname@domain.tld'),
            ],
        ]));

        return [
            [$uniqueModel, $user],
        ];
    }

    public function providerForValidInput()
    {
        $user = $this->getUser('id1', 'firstname.lastname@domain.tld');

        $uniqueModel = new UniqueModelRule(['email']);
        $uniqueModel->setRepository($this->getUserRepository([
            [
                'arguments' => [['email' => 'firstname.lastname@domain.tld']],
                'return' => $this->getUser('id1', 'firstname.lastname@domain.tld'),
            ],
        ]));

        return [
            [$uniqueModel, $user],
        ];
    }

    /**
     * @param string $email
     *
     * @return ValidatableModelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getUser(string $id, string $email)
    {
        $user = $this
            ->getMockBuilder(ValidatableModelInterface::class)
            ->setMethods(['getId'])
            ->getMockForAbstractClass()
        ;

        $user->id = $id;
        $user->email = $email;

        $user->expects(self::any())->method('getId')->willReturn($user->id);

        return $user;
    }

    /**
     * @return RepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getUserRepository(array $findOneByStack)
    {
        $userRepository = $this
            ->getMockBuilder(RepositoryInterface::class)
            ->setMethods(['findOneBy'])
            ->getMockForAbstractClass();

        $findOneByCount = 0;
        $userRepository
            ->expects(self::any())
            ->method('findOneBy')
            ->willReturnCallback(function (array $criteria) use (&$findOneByStack, &$findOneByCount) {
                ++$findOneByCount;
                $findOneBy = array_shift($findOneByStack);

                self::assertNotNull(
                    $findOneBy,
                    sprintf('There is no findOneBy info within $findOneByStack at %d call.', $findOneByCount)
                );

                self::assertSame($criteria, $findOneBy['arguments'][0]);

                if (isset($findOneBy['exception'])) {
                    throw $findOneBy['exception'];
                }

                return $findOneBy['return'];
            })
        ;

        return $userRepository;
    }
}
