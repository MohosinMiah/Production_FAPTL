<?php
/**
 * Created by VSCODE.
 * User: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * Date: 10/05/2022
 * Time: 14:20
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\PropertyImage;
use App\Rental\Repositories\Contracts\PropertyImageInterface;

class PropertyImageRepository extends BaseRepository implements PropertyImageInterface
{

    protected $model;

    /**
     *PropertyImageRepository constructor.
     * @param PropertyImage $model
     */
    function __construct(PropertyImage $model)
    {
        $this->model = $model;
    }

}
