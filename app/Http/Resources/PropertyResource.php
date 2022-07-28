<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:07
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use DB;

class PropertyResource extends JsonResource
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
			'id'              => $this->id,
			// 'property_units'  => DB::table( 'faptl_property_units' )->where( 'property_id', $this->id )->get(),
			'name'         => $this->name,
			'code'         => $this->code,
			'type'         => $this->type,
			'address'         => $this->address,
			'city'         => $this->city,
			'state'         => $this->state,
			'zip'         => $this->zip,
			'note'         => $this->note,
			'rent_amount'         => $this->rent_amount,
			'size'         => $this->size,
			'link'         => $this->link,
			'isAvailable' => $this->isAvailable,
			'isFeatured'         => $this->isFeatured,
			'isActive'         => $this->isActive,

			'assign_user'         => $this->assign_user,
			'short_description'         => $this->short_description,
			'long_description'         => $this->long_description,
			'number_units'         => $this->number_units,

			'has_parking'         => $this->has_parking,

			'has_security_gard'         => $this->has_security_gard,

			'has_electricity'         => $this->has_electricity,

			'has_gas'         => $this->has_gas,

			'has_swiming_pool'         => $this->has_swiming_pool,

			'deleted_by'        => $this->deleted_by,
			'created_by'        => $this->created_by,
			'updated_by'        => $this->updated_by,
			
			'deleted_at'        => $this->deleted_at,
			'created_at'        => $this->created_at,
			'updated_at'        => $this->updated_at,
        ];
    }

    /**
     * Units with zero active leases
     * @param $units
     * @return array
     */
    private function vacantUnits($units)
    {
        $vacant = [];
        foreach ($units as $unit) {
            if ($unit['leases_total'] == 0)
                $vacant[] = $unit;
        }
        return $vacant;
    }

	public function units(){
		return DB::table('faptl_property_units')->first();
	}
}
