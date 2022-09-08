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

class FTenantResource extends JsonResource
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
			'id'                    => $this->id,
			'name'         => $this->name,
			'date_of_birth'         => $this->date_of_birth,
			'id_number'         => $this->id_number,
			'passport_number'         => $this->passport_number,
			'gender'         => $this->gender,
			'tenant_number'         => $this->tenant_number,
			'phone'         => $this->phone,
			'email'         => $this->email,
			'city'         => $this->city,
			'state' => $this->state,
			'country'         => $this->country,
			'postal_code'         => $this->postal_code,

			
			'emergency_contact_name'         => $this->emergency_contact_name,
			'emergency_contact_phone'       => $this->emergency_contact_phone,
             'emergency_contact_email'    => $this->emergency_contact_name,


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
}
