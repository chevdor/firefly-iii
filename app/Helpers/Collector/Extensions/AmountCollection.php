<?php

/**
 * AmountCollection.php
 * Copyright (c) 2020 james@firefly-iii.org
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

namespace FireflyIII\Helpers\Collector\Extensions;

use FireflyIII\Helpers\Collector\GroupCollectorInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Trait AmountCollection
 */
trait AmountCollection
{
    /**
     * Get transactions with a specific amount.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function amountIs(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->where('source.amount', app('steam')->negative($amount));
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function amountIsNot(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->where('source.amount', '!=', app('steam')->negative($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions where the amount is less than.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function amountLess(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->where('destination.amount', '<=', app('steam')->positive($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions where the amount is more than.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function amountMore(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->where('destination.amount', '>=', app('steam')->positive($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions with a specific foreign amount.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function foreignAmountIs(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->whereNotNull('source.foreign_amount');
                $q->where('source.foreign_amount', app('steam')->negative($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions with a specific foreign amount.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function foreignAmountIsNot(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->whereNull('source.foreign_amount');
                $q->orWhere('source.foreign_amount', '!=', app('steam')->negative($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions where the amount is less than.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function foreignAmountLess(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->whereNotNull('destination.foreign_amount');
                $q->where('destination.foreign_amount', '<=', app('steam')->positive($amount));
            }
        );

        return $this;
    }

    /**
     * Get transactions where the amount is more than.
     *
     * @param string $amount
     *
     * @return GroupCollectorInterface
     */
    public function foreignAmountMore(string $amount): GroupCollectorInterface
    {
        $this->query->where(
            static function (EloquentBuilder $q) use ($amount) { // @phpstan-ignore-line
                $q->whereNotNull('destination.foreign_amount');
                $q->where('destination.foreign_amount', '>=', app('steam')->positive($amount));
            }
        );

        return $this;
    }
}
