<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Note;
use App\Exception\Entity\EntityNotFoundWhenDeleteException;
use App\Model\Query\NoteQueryModel;
use App\Model\Response\Entity\NotePaginationResponseModelEntity;
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

    /**
     * @throws EntityNotFoundWhenDeleteException
     * @throws \DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //        $pagination = $this->getNotes();
        //
        //        $page = $pagination->getCurrentPageNumber();
        //        $pages = PaginationService::getPages(pagination: $pagination);
        //
        //        for ($i = $page; $i <= $pages; ++$i) {
        //            $people[$i]['salt'] = mt_rand(000000, 999999);
        //        }

        do {
            $pagination = $this->getNotes(offset: $page ?? 0);

            $page = $pagination->getCurrentPageNumber();
            $pages = PaginationService::getPages(pagination: $pagination);
        } while ($page <= $pages);

        //        return new NotePaginationResponseModelEntity(
        //            count: $pagination->count(),
        //            page: $pagination->getCurrentPageNumber(),
        //            total: $pagination->getTotalItemCount(),
        //            pages: PaginationService::getPages(pagination: $pagination),
        //            items: $this->collection(notes: $pagination->getItems())
        //        );

        //        $offset = 1;
        //
        //        while (true) {
        //            $notes = $this->getNotes();
        //
        //            if (0 === $notes->count()) {
        //                break;
        //            }
        //
        //            $notes->map(function (Note $note) {
        //                $this->noteService->transaction(func: function () use ($note) {
        //                    $this->noteService->delete(entity: $note);
        //                });
        //            });
        //
        //            ++$offset;
        //        }

        return Command::SUCCESS;
    }

    /**
     * @throws \DateMalformedStringException
     *
     * @return PaginationInterface<int, Note>
     */
    private function getNotes(int $offset): PaginationInterface
    {
        return $this->noteService->pagination(
            queryModel: new NoteQueryModel()
                ->setOffset(offset: $offset)
                ->setUpdatedAtLess(updatedAtLess: new \DateTimeImmutable()->modify(modifier: '-30 days'))
                ->setOrderBy(orderBy: ['id' => 'ASC']),
        );
    }
}
