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
        $I->wantTo('Выполнить команду удаления старых заметок');

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
        $I->dontSeeInRepository(entity: Note::class, params: ['name' => 'Старая заметка']);
    }

    protected function mainProvider(): array
    {
        return [
            [
                'fixtures' => [
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'user' => ['email' => 'test_1@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_2',
                        'description' => 'Описание заметки_2',
                        'user' => ['email' => 'test_2@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_3',
                        'description' => 'Описание заметки_3',
                        'user' => ['email' => 'test_3@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_4',
                        'description' => 'Описание заметки_4',
                        'user' => ['email' => 'test_4@mail.ru'],
                    ],
                ],
            ],
        ];
    }
}
