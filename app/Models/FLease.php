<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 14/04/2020
 * Time: 12:58
 */

namespace App\Models;

use App\Traits\PropertyAccountsSetup;
use App\Traits\SearchableTrait;
use Illuminate\Support\Collection;
use DB;

class FLease extends BaseModel
{
    use SearchableTrait, PropertyAccountsSetup;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faptl_fLeases';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'unit_id',
        'lease_type',
        'rent_amount',
        'lease_start',
        'lease_end', // e.g apartment, commercial, duplex, house, mixed_use, other
        'deposit_amount',
        'late_fee_amount',
        'isActive',

        'created_by',
        'updated_by',
        'deleted_by', 

        'deleted_at'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'faptl_fLeases.id' => 1,
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landlord()
    {
        return $this->belongsTo(Landlord::class, 'landlord_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property_type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'property_id')->orderBy('unit_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leases()
    {
        return $this->hasMany(Lease::class, 'property_id')->orderBy('lease_number')
            ->where('terminated_on', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function extra_charges()
    {
        return $this->belongsToMany(ExtraCharge::class, 'extra_charge_properties',
            'property_id', 'extra_charge_id')
            ->withPivot('extra_charge_value', 'extra_charge_type', 'extra_charge_frequency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function late_fees()
    {
        return $this->belongsToMany(LateFee::class, 'late_fee_properties',
            'property_id', 'late_fee_id')
            ->withPivot('late_fee_value', 'late_fee_type', 'late_fee_frequency', 'grace_period');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function utility_costs()
    {
        return $this->belongsToMany(Utility::class, 'property_utility_costs',
            'property_id', 'utility_id')
            ->withPivot('utility_unit_cost', 'utility_base_fee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payment_methods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_properties',
            'property_id', 'payment_method_id')
            ->withPivot('payment_method_description');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function periods()
    {
        return $this->belongsToMany(Period::class, 'period_properties',
            'property_id', 'period_id');
    }

    /**
     * @return mixed
     */
    public function tenants()
    {
        return $this->tenants;
    }

    /**
     * @return Collection
     */
    public function getTenantsAttribute(): Collection
    {
        return $this->leases
            ->pluck('tenants')
            ->flatten(1)
            ->unique('id')
            ->sortBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notices()
    {
        return $this->hasMany(VacationNotice::class, 'property_id')->orderBy('created_at');
    }

    /**
     * @return int
     */
    public function getUnitTotalAttribute()
    {
        return $this->units()->count();
    }
}
