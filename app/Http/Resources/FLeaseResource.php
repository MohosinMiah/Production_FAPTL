<?php
/**
 * Created by VSCode.
 * FLease: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * WhatsApp: +8801857126452
 * Date: 17/12/2022
 * Time: 13:11
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use DB;

class FLeaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'id'                  => $this->id,
			'property_id'         => $this->property_id,
            'property'            => DB::table( 'faptl_properties' )->where( 'id', $this->property_id )->first(),
			'unit_id'             => $this->unit_id,
			'property_unit'       =>  $this->loadUnit( $this->unit_id ),
			'tenant_id'           => $this->tenant_id,
			'tenant'              => DB::table( 'faptl_tenants' )->where( 'id', $this->tenant_id )->first(),
			'rent_amount'         => $this->rent_amount,
			'lease_start'         => $this->lease_start,
			'lease_end'           => $this->lease_end,

			'security_deposit'     => $this->security_deposit,
			'pet_security_deposit' => $this->pet_security_deposit,

			'invoice_starting_date'        => $this->invoice_starting_date,
			'invoice_amount'       => $this->invoice_amount,
			'prorated_amount'      => $this->prorated_amount,
			'prorated_starting_date'   => $this->prorated_starting_date,

			'isActive'             => $this->isActive,

			'deleted_by'          => $this->deleted_by,
			'created_by'          => $this->created_by,
			'updated_by'          => $this->updated_by,
			
			'deleted_at'          => $this->deleted_at,
			'created_at'          => $this->created_at,
			'updated_at'          => $this->updated_at,

        ];
    }

	public function loadUnit( $unitID ) {

		if( $unitID > 0 || $unitID != NULL || $unitID != '' )
		{
			return DB::table( 'faptl_property_units' )->where( 'id', $this->unit_id )->first();
		}
		else
		{
			return 'NULL';
		}

	}
}
