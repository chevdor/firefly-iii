<?php

/*
 * MigrateRecurrenceType.php
 * Copyright (c) 2021 james@firefly-iii.org
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

namespace FireflyIII\Console\Commands\Upgrade;

use FireflyIII\Console\Commands\ShowsFriendlyMessages;
use FireflyIII\Models\Recurrence;
use FireflyIII\Models\RecurrenceTransaction;
use FireflyIII\Models\TransactionType;
use Illuminate\Console\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class MigrateRecurrenceType
 */
class MigrateRecurrenceType extends Command
{
    use ShowsFriendlyMessages;

    public const string CONFIG_NAME = '550_migrate_recurrence_type';

    protected $description = 'Migrate transaction type of recurring transaction.';

    protected $signature = 'firefly-iii:migrate-recurrence-type {--F|force : Force the execution of this command.}';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): int
    {
        if ($this->isExecuted() && true !== $this->option('force')) {
            $this->friendlyInfo('This command has already been executed.');

            return 0;
        }

        $this->migrateTypes();
        $this->markAsExecuted();


        return 0;
    }

    /**
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function isExecuted(): bool
    {
        $configVar = app('fireflyconfig')->get(self::CONFIG_NAME, false);
        return (bool)$configVar?->data;
    }

    /**
     *
     */
    private function migrateTypes(): void
    {
        $set = Recurrence::get();
        /** @var Recurrence $recurrence */
        foreach ($set as $recurrence) {
            if ($recurrence->transactionType->type !== TransactionType::INVALID) {
                $this->migrateRecurrence($recurrence);
            }
        }
    }

    /**
     * @param Recurrence $recurrence
     *
     * @return void
     */
    private function migrateRecurrence(Recurrence $recurrence): void
    {
        $originalType                    = $recurrence->transaction_type_id;
        $newType                         = $this->getInvalidType();
        $recurrence->transaction_type_id = $newType->id;
        $recurrence->save();
        /** @var RecurrenceTransaction $transaction */
        foreach ($recurrence->recurrenceTransactions as $transaction) {
            $transaction->transaction_type_id = $originalType;
            $transaction->save();
        }
        $this->friendlyInfo(sprintf('Updated recurrence #%d to new transaction type model.', $recurrence->id));
    }

    /**
     *
     */
    private function getInvalidType(): TransactionType
    {
        return TransactionType::whereType(TransactionType::INVALID)->firstOrCreate(['type' => TransactionType::INVALID]);
    }

    /**
     *
     */
    private function markAsExecuted(): void
    {
        app('fireflyconfig')->set(self::CONFIG_NAME, true);
    }
}
