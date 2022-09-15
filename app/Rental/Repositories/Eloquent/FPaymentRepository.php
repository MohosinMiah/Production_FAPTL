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

use App\Rental\Repositories\Contracts\FPaymentInterface;
use App\Models\FPayment;

class FPaymentRepository extends BaseRepository implements FPaymentInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param FLease $model
     */
    function __construct(FPayment $model)
    {
        $this->model = $model;
    }
}
