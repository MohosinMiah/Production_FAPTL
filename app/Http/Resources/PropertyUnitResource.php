<?php
/**
 * Created by VSCode.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * WhatsApp: ++8801773193256
 * Date: 25/06/2022
 * Time: 06:26
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyUnitResource extends JsonResource
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
			'id'                => $this->id,
			'property_id'       => $this->property_id,
			'type'              => $this->type,
			'name'              => $this->name,
			'floor'             => $this->floor,
			'rent'              => $this->rent,
			'unit_type'         => $this->unit_type,
			'garadge_fee'       => $this->garadge_fee,
			'electricity_fee'   => $this->electricity_fee,
			'gas_fee'           => $this->gas_fee,
			'water_fee'         => $this->water_fee,
			'service_fee'       => $this->service_fee,
			'size'              => $this->size,
			'total_room'        => $this->total_room,
			'bed_room'          => $this->bed_room,
			'bath_room'         => $this->bath_room,
			'balcony'           => $this->balcony,
			'note'              => $this->note,
			'isAvailable'       => $this->isAvailable,
			'isFeatured'        => $this->isFeatured,
			
			'isActive'          => $this->isActive,
			
			'deleted_by'        => $this->deleted_by,
			'created_by'        => $this->created_by,
			'updated_by'        => $this->updated_by,
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
}
