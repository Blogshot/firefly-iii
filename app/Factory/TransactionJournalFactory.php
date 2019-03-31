<?php

/**
 * TransactionJournalFactory.php
 * Copyright (c) 2018 thegrumpydictator@gmail.com
 *
 * This file is part of Firefly III.
 *
 * Firefly III is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Firefly III is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Firefly III. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Factory;

use Carbon\Carbon;
use Exception;
use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Models\Note;
use FireflyIII\Models\TransactionCurrency;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Models\TransactionType;
use FireflyIII\Repositories\Bill\BillRepositoryInterface;
use FireflyIII\Repositories\Budget\BudgetRepositoryInterface;
use FireflyIII\Repositories\Category\CategoryRepositoryInterface;
use FireflyIII\Repositories\Currency\CurrencyRepositoryInterface;
use FireflyIII\Repositories\PiggyBank\PiggyBankRepositoryInterface;
use FireflyIII\Repositories\TransactionType\TransactionTypeRepositoryInterface;
use FireflyIII\Support\NullArrayObject;
use FireflyIII\User;
use Illuminate\Support\Collection;
use Log;

/**
 * Class TransactionJournalFactory
 */
class TransactionJournalFactory
{
    /** @var BillRepositoryInterface */
    private $billRepository;
    /** @var BudgetRepositoryInterface */
    private $budgetRepository;
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;
    /** @var CurrencyRepositoryInterface */
    private $currencyRepository;
    /** @var array */
    private $fields;
    /** @var PiggyBankEventFactory */
    private $piggyEventFactory;
    /** @var PiggyBankRepositoryInterface */
    private $piggyRepository;
    /** @var TagFactory */
    private $tagFactory;
    /** @var TransactionFactory */
    private $transactionFactory;
    /** @var TransactionTypeRepositoryInterface */
    private $typeRepository;
    /** @var User The user */
    private $user;

    /**
     * Constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->fields = [
            // sepa
            'sepa_cc', 'sepa_ct_op', 'sepa_ct_id',
            'sepa_db', 'sepa_country', 'sepa_ep',
            'sepa_ci', 'sepa_batch_id',

            // dates
            'interest_date', 'book_date', 'process_date',
            'due_date', 'payment_date', 'invoice_date',

            // others
            'recurrence_id',  'internal_reference', 'bunq_payment_id',
            'import_hash', 'import_hash_v2', 'external_id', 'original_source'];


        if ('testing' === config('app.env')) {
            Log::warning(sprintf('%s should not be instantiated in the TEST environment!', \get_class($this)));
        }

        $this->currencyRepository = app(CurrencyRepositoryInterface::class);
        $this->typeRepository     = app(TransactionTypeRepositoryInterface::class);
        $this->transactionFactory = app(TransactionFactory::class);
        $this->billRepository     = app(BillRepositoryInterface::class);
        $this->budgetRepository   = app(BudgetRepositoryInterface::class);
        $this->categoryRepository = app(CategoryRepositoryInterface::class);
        $this->piggyRepository    = app(PiggyBankRepositoryInterface::class);
        $this->piggyEventFactory  = app(PiggyBankEventFactory::class);
        $this->tagFactory         = app(TagFactory::class);
    }

    /**
     * Store a new (set of) transaction journals.
     *
     * @param array $data
     *
     * @return Collection
     * @throws FireflyException
     */
    public function create(array $data): Collection
    {
        // convert to special object.
        $data = new NullArrayObject($data);

        Log::debug('Start of TransactionJournalFactory::create()');
        $collection   = new Collection;
        $transactions = $data['transactions'] ?? [];
        if (0 === count($transactions)) {
            Log::error('There are no transactions in the array, the TransactionJournalFactory cannot continue.');

            return new Collection;
        }

        /** @var array $row */
        foreach ($transactions as $index => $row) {
            Log::debug(sprintf('Now creating journal %d/%d', $index + 1, count($transactions)));

            $journal = $this->createJournal(new NullArrayObject($row));
            if (null !== $journal) {
                $collection->push($journal);
            }
        }

        return $collection;
    }

    /**
     * Set the user.
     *
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->currencyRepository->setUser($this->user);
        $this->transactionFactory->setUser($this->user);
        $this->billRepository->setUser($this->user);
        $this->budgetRepository->setUser($this->user);
        $this->categoryRepository->setUser($this->user);
        $this->piggyRepository->setUser($this->user);
    }

    /**
     * Link a piggy bank to this journal.
     *
     * @param TransactionJournal $journal
     * @param NullArrayObject    $data
     */
    public function storePiggyEvent(TransactionJournal $journal, NullArrayObject $data): void
    {
        Log::debug('Will now store piggy event.');
        if (!$journal->isTransfer()) {
            Log::debug('Journal is not a transfer, do nothing.');

            return;
        }

        $piggyBank = $this->piggyRepository->findPiggyBank($data['piggy_bank'], (int)$data['piggy_bank_id'], $data['piggy_bank_name']);

        if (null !== $piggyBank) {
            $this->piggyEventFactory->create($journal, $piggyBank);
            Log::debug('Create piggy event.');

            return;
        }
        Log::debug('Create no piggy event');
    }

    /**
     * Link tags to journal.
     *
     * @param TransactionJournal $journal
     * @param array              $tags
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function storeTags(TransactionJournal $journal, ?array $tags): void
    {
        $this->tagFactory->setUser($journal->user);
        $set = [];
        if (!\is_array($tags)) {
            return;
        }
        foreach ($tags as $string) {
            if ('' !== $string) {
                $tag = $this->tagFactory->findOrCreate($string);
                if (null !== $tag) {
                    $set[] = $tag->id;
                }
            }
        }
        $journal->tags()->sync($set);
    }

    /**
     * @param TransactionJournal $journal
     * @param NullArrayObject    $data
     * @param string             $field
     */
    protected function storeMeta(TransactionJournal $journal, NullArrayObject $data, string $field): void
    {
        $set = [
            'journal' => $journal,
            'name'    => $field,
            'data'    => (string)($data[$field] ?? ''),
        ];

        Log::debug(sprintf('Going to store meta-field "%s", with value "%s".', $set['name'], $set['data']));

        /** @var TransactionJournalMetaFactory $factory */
        $factory = app(TransactionJournalMetaFactory::class);
        $factory->updateOrCreate($set);
    }

    /**
     * @param TransactionJournal $journal
     * @param string             $notes
     */
    protected function storeNote(TransactionJournal $journal, ?string $notes): void
    {
        $notes = (string)$notes;
        if ('' !== $notes) {
            $note = $journal->notes()->first();
            if (null === $note) {
                $note = new Note;
                $note->noteable()->associate($journal);
            }
            $note->text = $notes;
            $note->save();
            Log::debug(sprintf('Stored notes for journal #%d', $journal->id));

            return;
        }
    }

    /**
     * @param NullArrayObject $row
     *
     * @return TransactionJournal|null
     * @throws FireflyException
     */
    private function createJournal(NullArrayObject $row): ?TransactionJournal
    {
        $row['import_hash_v2'] = $this->hashArray($row);

        /** Get basic fields */
        $type            = $this->typeRepository->findTransactionType(null, $row['type']);
        $carbon          = $row['date'] ?? new Carbon;
        $currency        = $this->currencyRepository->findCurrency((int)$row['currency_id'], $row['currency_code']);
        $foreignCurrency = $this->findForeignCurrency($row);
        $bill            = $this->billRepository->findBill((int)$row['bill_id'], $row['bill_name']);
        $billId          = TransactionType::WITHDRAWAL === $type->type && null !== $bill ? $bill->id : null;
        $description     = app('steam')->cleanString((string)$row['description']);

        /** Manipulate basic fields */
        $carbon->setTimezone(config('app.timezone'));

        /** Create a basic journal. */
        $journal = TransactionJournal::create(
            [
                'user_id'                 => $this->user->id,
                'transaction_type_id'     => $type->id,
                'bill_id'                 => $billId,
                'transaction_currency_id' => $currency->id,
                'description'             => '' === $description ? '(empty description)' : $description,
                'date'                    => $carbon->format('Y-m-d H:i:s'),
                'order'                   => 0,
                'tag_count'               => 0,
                'completed'               => 0,
            ]
        );
        Log::debug(sprintf('Created new journal #%d: "%s"', $journal->id, $journal->description));

        /** Create two transactions. */
        $this->transactionFactory->setJournal($journal);
        $this->transactionFactory->createPair($row, $currency, $foreignCurrency);

        // verify that journal has two transactions. Otherwise, delete and cancel.
        $count = $journal->transactions()->count();
        if (2 !== $count) {
            // @codeCoverageIgnoreStart
            Log::error(sprintf('The journal unexpectedly has %d transaction(s). This is not OK. Cancel operation.', $count));
            try {
                $journal->delete();
            } catch (Exception $e) {
                Log::debug(sprintf('Dont care: %s.', $e->getMessage()));
            }

            return null;
            // @codeCoverageIgnoreEnd
        }
        $journal->completed = true;
        $journal->save();

        /** Link all other data to the journal. */

        /** Link budget */
        $this->storeBudget($journal, $row);

        /** Link category */
        $this->storeCategory($journal, $row);

        /** Set notes */
        $this->storeNote($journal, $row['notes']);

        /** Set piggy bank */
        $this->storePiggyEvent($journal, $row);

        /** Set tags */
        $this->storeTags($journal, $row['tags']);

        /** set all meta fields */
        $this->storeMetaFields($journal, $row);

        return $journal;
    }

    /**
     * This is a separate function because "findCurrency" will default to EUR and that may not be what we want.
     *
     * @param NullArrayObject $transaction
     *
     * @return TransactionCurrency|null
     */
    private function findForeignCurrency(NullArrayObject $transaction): ?TransactionCurrency
    {
        if (null === $transaction['foreign_currency'] && null === $transaction['foreign_currency_id'] && null === $transaction['foreign_currency_code']) {
            return null;
        }

        return $this->currencyRepository->findCurrency((int)$transaction['foreign_currency_id'], $transaction['foreign_currency_code']);
    }

    /**
     * @param NullArrayObject $row
     *
     * @return string
     */
    private function hashArray(NullArrayObject $row): string
    {
        $row['import_hash_v2']    = null;
        $row['original_source'] = null;
        $json                   = json_encode($row);
        if (false === $json) {
            $json = json_encode((string)microtime());
        }
        $hash = hash('sha256', $json);
        Log::debug(sprintf('The hash is: %s', $hash));

        return $hash;
    }


    /**
     * @param TransactionJournal $journal
     * @param NullArrayObject    $data
     */
    private function storeBudget(TransactionJournal $journal, NullArrayObject $data): void
    {
        if (TransactionType::WITHDRAWAL !== $journal->transactionType->type) {
            return;
        }
        $budget = $this->budgetRepository->findBudget($data['budget'], $data['budget_id'], $data['budget_name']);
        if (null !== $budget) {
            Log::debug(sprintf('Link budget #%d to journal #%d', $budget->id, $journal->id));
            $journal->budgets()->sync([$budget->id]);
        }
    }

    /**
     * @param TransactionJournal $journal
     * @param NullArrayObject    $data
     */
    private function storeCategory(TransactionJournal $journal, NullArrayObject $data): void
    {
        $category = $this->categoryRepository->findCategory($data['category'], $data['category_id'], $data['category_name']);
        if (null !== $category) {
            Log::debug(sprintf('Link category #%d to journal #%d', $category->id, $journal->id));
            $journal->categories()->sync([$category->id]);
        }
    }

    /**
     * @param TransactionJournal $journal
     * @param NullArrayObject    $transaction
     */
    private function storeMetaFields(TransactionJournal $journal, NullArrayObject $transaction): void
    {
        foreach ($this->fields as $field) {
            $this->storeMeta($journal, $transaction, $field);
        }
    }


}
