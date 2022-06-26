<?php
/**
 * Created by PhpStorm.
 * Property: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 14/04/2020
 * Time: 13:11
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\PropertyUnitImageInterface;
use App\Models\PropertyUnitImage;

class PropertyUnitImageRepository extends BaseRepository implements PropertyUnitImageInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Property $model
     */
    function __construct(PropertyUnitImage $model)
    {
        $this->model = $model;
    }
}
