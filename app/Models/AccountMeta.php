<?php

/**
 * AccountMeta.php
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

namespace FireflyIII\Models;

use Carbon\Carbon;
use Eloquent;
use FireflyIII\Support\Models\ReturnsIntegerIdTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AccountMeta
 *
 * @property int          $id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property int          $account_id
 * @property string       $name
 * @property mixed        $data
 * @property-read Account $account
 * @method static Builder|AccountMeta newModelQuery()
 * @method static Builder|AccountMeta newQuery()
 * @method static Builder|AccountMeta query()
 * @method static Builder|AccountMeta whereAccountId($value)
 * @method static Builder|AccountMeta whereCreatedAt($value)
 * @method static Builder|AccountMeta whereData($value)
 * @method static Builder|AccountMeta whereId($value)
 * @method static Builder|AccountMeta whereName($value)
 * @method static Builder|AccountMeta whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AccountMeta extends Model
{
    use ReturnsIntegerIdTrait;

    protected $casts
        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

    protected $fillable = ['account_id', 'name', 'data'];
    /** @var string The table to store the data in */
    protected $table = 'account_meta';

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getDataAttribute(mixed $value): string
    {
        return (string)json_decode($value, true);
    }

    /**
     * @param mixed $value
     */
    public function setDataAttribute(mixed $value): void
    {
        $this->attributes['data'] = json_encode($value);
    }

}
