<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Note;
use App\Model\Query\NoteQueryModel;
use App\Service\Entity\NoteService;
use App\Service\PaginationService;
use Knp\Component\Pager\Pagination\PaginationInterface;
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
)]
#[AsCronTask(expression: '0 0 * * *')]
class NotesDeleteCommand extends Command
{
    public function __construct(private readonly NoteService $noteService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->noteService->transaction(func: function () {
            do {
                $pagination = $this->getNotes();

                $page = $pagination->getCurrentPageNumber();
                $pages = PaginationService::getPages(pagination: $pagination);

                array_map(
                    fn (Note $note) => $this->noteService->delete(entity: $note),
                    $pagination->getItems()
                );
            } while ($page <= $pages);
        });

        return Command::SUCCESS;
    }

    /**
     * @throws \DateMalformedStringException
     *
     * @return PaginationInterface<int, Note>
     */
    private function getNotes(): PaginationInterface
    {
        return $this->noteService->pagination(
            queryModel: new NoteQueryModel()
                ->setIsTrashed(isTrashed: true)
                ->setDeletedAtLess(deletedAtLess: new \DateTimeImmutable()->modify(modifier: '-30 days'))
                ->setOrderBy(orderBy: ['id' => 'ASC']),
        );
    }
}
