<?php
/**
 * RemoveFromDescription.php
 * Copyright (c) 2023 sascha@knott.ac
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace FireflyIII\TransactionRules\Actions;

use DB;
use FireflyIII\Models\RuleAction;

/**
 * Class RemoveFromDescription.
 */
class RemoveFromDescription implements ActionInterface
{
    /** @var RuleAction The rule action */
    private $action;

    /**
     * TriggerInterface constructor.
     *
     * @param RuleAction $action
     */
    public function __construct(RuleAction $action)
    {
        $this->action = $action;
    }

    /**
     * @inheritDoc
     */
    public function actOnArray(array $journal): bool
    {
        $action_value = $this->action->action_value;
        $before = $journal['description'];

        // replace all occurrences
        $after = str_replace($action_value, '', $before);

        DB::table('transaction_journals')->where('id', $journal['transaction_journal_id'])->limit(1)->update(['description' => $after]);

        // journal
        /** @var TransactionJournal $object */
        $object = TransactionJournal::where('user_id', $journal['user_id'])->find($journal['transaction_journal_id']);

        // audit log
        event(new TriggeredAuditLog($this->action->rule, $object, 'update_description', $before, $after));

        return true;
    }
}
