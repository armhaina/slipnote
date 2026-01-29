<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Entity\Note;
use App\Tests\_data\fixtures\NoteFixtures;
use App\Tests\Functional\AbstractCest;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

final class CronNotesDeleteCest extends AbstractCest
{
    /**
     * @throws \Exception
     */
    #[DataProvider('mainProvider')]
    public function main(FunctionalTester $I, Example $example): void
    {
        $I->wantTo('COMMAND/200: Удалить заметки из корзины');

        foreach ($example['fixtures'] as $fixture) {
            NoteFixtures::load(I: $I, data: $fixture);
        }

        /** @var KernelInterface $kernel */
        $kernel = $I->grabService(serviceId: 'kernel');

        $application = new Application(kernel: $kernel);
        $application->setAutoExit(boolean: false);

        $input = new ArrayInput(parameters: ['command' => 'cron:notes-delete']);
        $output = new BufferedOutput();

        $exitCode = $application->run(input: $input, output: $output);

        $I->assertEquals(expected: 0, actual: $exitCode);

        $I->seeInRepository(entity: Note::class, params: ['name' => 'Заметка_1']);
        $I->seeInRepository(entity: Note::class, params: ['name' => 'Заметка_2']);
        $I->seeInRepository(entity: Note::class, params: ['name' => 'Заметка_3']);
        $I->seeInRepository(entity: Note::class, params: ['name' => 'Заметка_4']);

        $I->dontSeeInRepository(entity: Note::class, params: ['name' => 'Заметка_5']);
    }

    /**
     * @throws \DateMalformedStringException
     */
    protected function mainProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => 'test_1@mail.ru'],
                    ], // НЕ удаляем
                    [
                        'name' => 'Заметка_2',
                        'description' => 'Описание заметки_2',
                        'is_trashed' => true,
                        'deleted_at' => new \DateTimeImmutable()->modify(modifier: '-15 days'),
                        'user' => ['email' => 'test_2@mail.ru'],
                    ], // НЕ удаляем потому что deleted_at меньше 30 дней
                    [
                        'name' => 'Заметка_3',
                        'description' => 'Описание заметки_3',
                        'deleted_at' => new \DateTimeImmutable()->modify(modifier: '-15 days'),
                        'user' => ['email' => 'test_3@mail.ru'],
                    ], // НЕ удаляем потому что is_trash = false
                    [
                        'name' => 'Заметка_4',
                        'description' => 'Описание заметки_4',
                        'is_trashed' => true,
                        'user' => ['email' => 'test_4@mail.ru'],
                    ], // НЕ удаляем потому что deleted_at не заполнено
                    [
                        'name' => 'Заметка_5',
                        'description' => 'Описание заметки_5',
                        'is_trashed' => true,
                        'deleted_at' => new \DateTimeImmutable()->modify(modifier: '-31 days'),
                        'user' => ['email' => 'test_5@mail.ru'],
                    ], // УДАЛЯЕМ
                ],
            ],
        ];
    }
}
