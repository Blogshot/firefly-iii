<?php

/**
 * User.php
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

namespace FireflyIII;

use Eloquent;
use Exception;
use FireflyIII\Events\RequestedNewPassword;
use FireflyIII\Models\Account;
use FireflyIII\Models\Attachment;
use FireflyIII\Models\AvailableBudget;
use FireflyIII\Models\Bill;
use FireflyIII\Models\Budget;
use FireflyIII\Models\Category;
use FireflyIII\Models\CurrencyExchangeRate;
use FireflyIII\Models\GroupMembership;
use FireflyIII\Models\ObjectGroup;
use FireflyIII\Models\PiggyBank;
use FireflyIII\Models\Preference;
use FireflyIII\Models\Recurrence;
use FireflyIII\Models\Role;
use FireflyIII\Models\Rule;
use FireflyIII\Models\RuleGroup;
use FireflyIII\Models\Tag;
use FireflyIII\Models\Transaction;
use FireflyIII\Models\TransactionGroup;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Models\UserGroup;
use FireflyIII\Models\Webhook;
use FireflyIII\Notifications\Admin\TestNotification;
use FireflyIII\Notifications\Admin\UserInvitation;
use FireflyIII\Notifications\Admin\UserRegistration;
use FireflyIII\Notifications\Admin\VersionCheckResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class User.
 *
 * @property int $id
 * @property string $email
 * @property bool $isAdmin
 * @property bool $has2FA
 * @property array $prefs
 * @property string $password
 * @property string $mfa_secret
 * @property Collection $roles
 * @property string $blocked_code
 * @property bool $blocked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $remember_token
 * @property string|null $reset
 * @property-read \Illuminate\Database\Eloquent\Collection|Account[] $accounts
 * @property-read \Illuminate\Database\Eloquent\Collection|Attachment[] $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|AvailableBudget[] $availableBudgets
 * @property-read \Illuminate\Database\Eloquent\Collection|Bill[] $bills
 * @property-read \Illuminate\Database\Eloquent\Collection|Budget[] $budgets
 * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|CurrencyExchangeRate[] $currencyExchangeRates
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|PiggyBank[] $piggyBanks
 * @property-read \Illuminate\Database\Eloquent\Collection|Preference[] $preferences
 * @property-read \Illuminate\Database\Eloquent\Collection|Recurrence[] $recurrences
 * @property-read \Illuminate\Database\Eloquent\Collection|RuleGroup[] $ruleGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|Rule[] $rules
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|Token[] $tokens
 * @property-read \Illuminate\Database\Eloquent\Collection|TransactionGroup[] $transactionGroups
 * @property-read \Illuminate\Database\Eloquent\Collection|TransactionJournal[] $transactionJournals
 * @property-read \Illuminate\Database\Eloquent\Collection|Transaction[] $transactions
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereBlocked($value)
 * @method static Builder|User whereBlockedCode($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereReset($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 * @property string|null $objectguid
 * @property-read int|null $accounts_count
 * @property-read int|null $attachments_count
 * @property-read int|null $available_budgets_count
 * @property-read int|null $bills_count
 * @property-read int|null $budgets_count
 * @property-read int|null $categories_count
 * @property-read int|null $clients_count
 * @property-read int|null $currency_exchange_rates_count
 * @property-read int|null $notifications_count
 * @property-read int|null $piggy_banks_count
 * @property-read int|null $preferences_count
 * @property-read int|null $recurrences_count
 * @property-read int|null $roles_count
 * @property-read int|null $rule_groups_count
 * @property-read int|null $rules_count
 * @property-read int|null $tags_count
 * @property-read int|null $tokens_count
 * @property-read int|null $transaction_groups_count
 * @property-read int|null $transaction_journals_count
 * @property-read int|null $transactions_count
 * @method static Builder|User whereMfaSecret($value)
 * @method static Builder|User whereObjectguid($value)
 * @property string|null $provider
 * @method static Builder|User whereProvider($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|ObjectGroup[] $objectGroups
 * @property-read int|null $object_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Webhook[] $webhooks
 * @property-read int|null $webhooks_count
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $guid
 * @property string|null $domain
 * @method static Builder|User whereDomain($value)
 * @method static Builder|User whereGuid($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @property int|null $user_group_id
 * @property-read \Illuminate\Database\Eloquent\Collection|GroupMembership[] $groupMemberships
 * @property-read int|null $group_memberships_count
 * @property-read UserGroup|null $userGroup
 * @method static Builder|User whereUserGroupId($value)
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'blocked'    => 'boolean',
        ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'password', 'blocked', 'blocked_code'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * @param  string  $value
     *
     * @return User
     * @throws NotFoundHttpException
     */
    public static function routeBinder(string $value): User
    {
        if (auth()->check()) {
            $userId = (int)$value;
            $user   = self::find($userId);
            if (null !== $user) {
                return $user;
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @codeCoverageIgnore
     * Link to accounts.
     *
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to attachments
     *
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to available budgets
     *
     * @return HasMany
     */
    public function availableBudgets(): HasMany
    {
        return $this->hasMany(AvailableBudget::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to bills.
     *
     * @return HasMany
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to budgets.
     *
     * @return HasMany
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to categories
     *
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to currency exchange rates
     *
     * @return HasMany
     */
    public function currencyExchangeRates(): HasMany
    {
        return $this->hasMany(CurrencyExchangeRate::class);
    }

    /**
     * @codeCoverageIgnore
     * Generates access token.
     *
     * @return string
     * @throws Exception
     */
    public function generateAccessToken(): string
    {
        $bytes = random_bytes(16);

        return bin2hex($bytes);
    }

    /**
     * Get the models LDAP domain.
     * @return string
     * @deprecated
     *
     */
    public function getLdapDomain()
    {
        return $this->{$this->getLdapDomainColumn()};
    }

    /**
     * Get the database column name of the domain.
     * @return string
     * @deprecated
     *
     */
    public function getLdapDomainColumn()
    {
        return 'domain';
    }

    /**
     * Get the models LDAP GUID.
     * @return string
     * @deprecated
     *
     */
    public function getLdapGuid()
    {
        return $this->{$this->getLdapGuidColumn()};
    }

    /**
     * Get the models LDAP GUID database column name.
     * @return string
     * @deprecated
     *
     */
    public function getLdapGuidColumn()
    {
        return 'objectguid';
    }

    /**
     * @codeCoverageIgnore
     *
     * @return HasMany
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMembership::class)->with(['userGroup', 'userRole']);
    }

    /**
     * @codeCoverageIgnore
     * Link to object groups.
     *
     * @return HasMany
     */
    public function objectGroups(): HasMany
    {
        return $this->hasMany(ObjectGroup::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to piggy banks.
     *
     * @return HasManyThrough
     */
    public function piggyBanks(): HasManyThrough
    {
        return $this->hasManyThrough(PiggyBank::class, Account::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to preferences.
     *
     * @return HasMany
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(Preference::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to recurring transactions.
     *
     * @return HasMany
     */
    public function recurrences(): HasMany
    {
        return $this->hasMany(Recurrence::class);
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        if (method_exists($this, $method = 'routeNotificationFor'.Str::studly($driver))) {
            return $this->{$method}($notification);
        }
        $email = $this->email;
        // see if user has alternative email address:
        $pref = app('preferences')->getForUser($this, 'remote_guard_alt_email');
        if (null !== $pref) {
            $email = $pref->data;
        }
        // if user is demo user, send to owner:
        if ($this->hasRole('demo')) {
            $email = config('firefly.site_owner');
        }

        return match ($driver) {
            'database' => $this->notifications(),
            'mail' => $email,
            default => null,
        };
    }

    /**
     * @param  string  $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->count() === 1;
    }

    /**
     * @codeCoverageIgnore
     * Link to roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param  Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack(Notification $notification): string
    {
        // this check does not validate if the user is owner, Should be done by notification itself.
        if ($notification instanceof TestNotification) {
            return app('fireflyconfig')->get('slack_webhook_url', '')->data;
        }
        if ($notification instanceof UserRegistration) {
            return app('fireflyconfig')->get('slack_webhook_url', '')->data;
        }
        if ($notification instanceof VersionCheckResult) {
            return app('fireflyconfig')->get('slack_webhook_url', '')->data;
        }
        if ($notification instanceof UserInvitation) {
            return app('fireflyconfig')->get('slack_webhook_url', '')->data;
        }
        return app('preferences')->getForUser($this, 'slack_webhook_url', '')->data;
    }

    /**
     * @codeCoverageIgnore
     * Link to rule groups.
     *
     * @return HasMany
     */
    public function ruleGroups(): HasMany
    {
        return $this->hasMany(RuleGroup::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to rules.
     *
     * @return HasMany
     */
    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * @codeCoverageIgnore
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $ipAddress = Request::ip();

        event(new RequestedNewPassword($this, $token, $ipAddress));
    }

    // start LDAP related code

    /**
     * Set the models LDAP domain.
     * @param  string  $domain
     *
     * @return void
     * @deprecated
     *
     */
    public function setLdapDomain($domain)
    {
        $this->{$this->getLdapDomainColumn()} = $domain;
    }

    /**
     * Set the models LDAP GUID.
     * @param  string  $guid
     *
     * @return void
     * @deprecated
     */
    public function setLdapGuid($guid)
    {
        $this->{$this->getLdapGuidColumn()} = $guid;
    }

    /**
     * @codeCoverageIgnore
     * Link to tags.
     *
     * @return HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to transaction groups.
     *
     * @return HasMany
     */
    public function transactionGroups(): HasMany
    {
        return $this->hasMany(TransactionGroup::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to transaction journals.
     *
     * @return HasMany
     */
    public function transactionJournals(): HasMany
    {
        return $this->hasMany(TransactionJournal::class);
    }

    /**
     * @codeCoverageIgnore
     * Link to transactions.
     *
     * @return HasManyThrough
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, TransactionJournal::class);
    }

    /**
     * @codeCoverageIgnore
     * @return BelongsTo
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, );
    }

    /**
     * @codeCoverageIgnore
     *
     * Link to webhooks
     *
     * @return HasMany
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
