<?php
/**
 * MonthReportGenerator.php
 * Copyright (c) 2019 james@firefly-iii.org
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

namespace FireflyIII\Generator\Report\Tag;

use Carbon\Carbon;
use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Generator\Report\ReportGeneratorInterface;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Class MonthReportGenerator.
 *

 */
class MonthReportGenerator implements ReportGeneratorInterface
{
    private Collection $accounts;
    private Carbon     $end;
    private Carbon     $start;
    private Collection $tags;

    /**
     * MonthReportGenerator constructor.
     */
    public function __construct()
    {
        $this->tags     = new Collection();
        $this->accounts = new Collection();
    }

    /**
     * Generate the report.
     *
     * @return string
     * @throws FireflyException
     */
    public function generate(): string
    {
        $accountIds = implode(',', $this->accounts->pluck('id')->toArray());
        $tagIds     = implode(',', $this->tags->pluck('id')->toArray());
        $reportType = 'tag';

        // render!
        try {
            $result = view(
                'reports.tag.month',
                compact('accountIds', 'reportType', 'tagIds')
            )->with('start', $this->start)->with('end', $this->end)->with('tags', $this->tags)->with('accounts', $this->accounts)->render();
        } catch (Throwable $e) {
            app('log')->error(sprintf('Cannot render reports.tag.month: %s', $e->getMessage()));
            app('log')->error($e->getTraceAsString());
            $result = sprintf('Could not render report view: %s', $e->getMessage());
            throw new FireflyException($result, 0, $e);
        }

        return $result;
    }

    /**
     * Set the accounts.
     *
     * @param Collection $accounts
     *
     * @return ReportGeneratorInterface
     */
    public function setAccounts(Collection $accounts): ReportGeneratorInterface
    {
        $this->accounts = $accounts;

        return $this;
    }

    /**
     * Unused budget setter.
     *
     * @param Collection $budgets
     *
     * @return ReportGeneratorInterface
     */
    public function setBudgets(Collection $budgets): ReportGeneratorInterface
    {
        return $this;
    }

    /**
     * Unused category setter.
     *
     * @param Collection $categories
     *
     * @return ReportGeneratorInterface
     */
    public function setCategories(Collection $categories): ReportGeneratorInterface
    {
        return $this;
    }

    /**
     * Set the end date of the report.
     *
     * @param Carbon $date
     *
     * @return ReportGeneratorInterface
     */
    public function setEndDate(Carbon $date): ReportGeneratorInterface
    {
        $this->end = $date;

        return $this;
    }

    /**
     * Set the expenses in this report.
     *
     * @param Collection $expense
     *
     * @return ReportGeneratorInterface
     */
    public function setExpense(Collection $expense): ReportGeneratorInterface
    {
        return $this;
    }

    /**
     * Set the start date.
     *
     * @param Carbon $date
     *
     * @return ReportGeneratorInterface
     */
    public function setStartDate(Carbon $date): ReportGeneratorInterface
    {
        $this->start = $date;

        return $this;
    }

    /**
     * Set the tags used in this report.
     *
     * @param Collection $tags
     *
     * @return ReportGeneratorInterface
     */
    public function setTags(Collection $tags): ReportGeneratorInterface
    {
        $this->tags = $tags;

        return $this;
    }
}
