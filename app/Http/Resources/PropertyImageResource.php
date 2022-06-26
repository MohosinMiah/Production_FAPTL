<?php
/**
 * Created by VSCODE.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * Date: 10/05/2022
 * Time: 14:20
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                => $this->id,
            'property_id'         => $this->property_id,
            'file_name'         => $this->file_name,
            'alt_text'         => $this->alt_text,
            'isActive'         => $this->isActive,
            'isFeatured'    => $this->isFeatured,

            'deleted_at'        => $this->deleted_at,
            'created_by'    => $this->created_by,
            'updated_by'      => $this->updated_by,
            'deleted_by'      => $this->deleted_by,
        ];

        return $data;
    }

    
}

