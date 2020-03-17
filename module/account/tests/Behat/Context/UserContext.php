<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Account\Tests\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Ergonode\Account\Domain\Command\User\CreateUserCommand;
use Ergonode\Account\Domain\ValueObject\Password;
use Ergonode\Core\Domain\ValueObject\Language;
use Ergonode\SharedKernel\Domain\Aggregate\MultimediaId;
use Ergonode\SharedKernel\Domain\Aggregate\RoleId;
use Ergonode\SharedKernel\Domain\Aggregate\UserId;
use Ergonode\SharedKernel\Domain\ValueObject\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Faker\Generator;

/**
 */
class UserContext
{
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    /**
     * @var Generator
     */
    private Generator $generator;

    public function __construct(MessageBusInterface $messageBus, Generator $generator)
    {
        $this->messageBus = $messageBus;
        $this->generator = $generator;
    }

    /**
     *  @Given there are following users:
     * | email          |  role
     * | some@email.com |  Admin
     *
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function thereAreFollowingUsers(TableNode $table) : void
    {
        foreach ($table as $row) {
            $row['first_name'] = $row['first_name'] ?? $this->generator->firstName;
            $row['last_name'] = $row['last_name'] ?? $this->generator->lastName;

            $email = $row['email'] ?? $this->generator->email;
            $language = $row['language'] ?? $this->generator->randomElement(Language::AVAILABLE);
            $password = $row['password'] ?? $this->generator->password(Password::MIN_LENGTH, Password::MAX_LENGTH);
            $roleId = $row['role'] ?? 'Admin';
            $isActive = $row['is_active'] ?? true;
            $avatarId = $row['avatar_id'] ?? null;
            /*$uuid = $row['uuid'] ?? null;
            if ($uuid) {
                $uuid = new UserId($uuid);
            }*/
            if ($avatarId) {
                $avatarId = new MultimediaId($avatarId);
            }

            $command = new CreateUserCommand(
                $row['first_name'] ?? $this->generator->firstName,
                $row['last_name'] ?? $this->generator->lastName,
                new Email($email),
                Language::fromString($language),
                new Password($password),
                new RoleId($roleId),
                $isActive,
                $avatarId,
                /*$uuid*/
            );
            $this->messageBus->dispatch($command);
        }
    }
}
