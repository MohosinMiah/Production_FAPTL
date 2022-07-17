<?php
/**
 * Created by VSCode.
 * FLease: MD MOHOSIN MIAH
 * Email: mohosin.csm@gmail.com
 * WhatsApp: +8801857126452
 * Date: 17/12/2022
 * Time: 13:11
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\FLeaseInterface;
use App\Models\FLease;

class FLeaseRepository extends BaseRepository implements FLeaseInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param FLease $model
     */
    function __construct(FLease $model)
    {
        $this->model = $model;
    }
}
