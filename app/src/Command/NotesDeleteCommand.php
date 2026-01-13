<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Note;
use App\Exception\EntityQueryModel\EntityQueryModelInvalidObjectTypeException;
use App\Model\Query\NoteQueryModel;
use App\Service\NoteService;
use Ds\Sequence;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

/**
 * Запуск каждый день в полночь.
 */
#[AsCommand(
    name: 'cron:notes-delete',
    description: 'Notes delete command'
)
]
#[AsCronTask(expression: '0 0 * * *')]
class NotesDeleteCommand extends Command
{
    private const LIMIT = 20;

    public function __construct(private readonly NoteService $noteService)
    {
        parent::__construct();
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $offset = 1;

        while (true) {
            $notes = $this->getNotes(
                offset: ($offset - 1) * 20,
            );

            if (0 === $notes->count()) {
                break;
            }

            $notes->map(function (Note $note) {
                $this->noteService->transaction(func: function () use ($note) {
                    $this->noteService->delete(entity: $note);
                });
            });

            ++$offset;
        }

        return Command::SUCCESS;
    }

    /**
     * @throws EntityQueryModelInvalidObjectTypeException
     */
    private function getNotes(int $offset): Sequence
    {
        return $this->noteService->list(
            queryModel: (new NoteQueryModel())
                ->setUpdatedAtLess(updatedAtLess: (new \DateTimeImmutable())->modify(modifier: '-30 days'))
                ->setOffset(offset: $offset)
                ->setLimit(limit: self::LIMIT)
                ->setOrderBy(orderBy: ['id' => 'ASC']),
        );
    }
}
