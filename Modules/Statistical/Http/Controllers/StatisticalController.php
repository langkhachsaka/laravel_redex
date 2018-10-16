<?php

namespace Modules\Statistical\Http\Controllers;

use Modules\Base\Http\Controllers\Controller;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Statistical\Models\Statistical;

class StatisticalController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getSumCustomerOrder()
    {
        $this->authorize('index', Statistical::class);
        $sumCustomerOrders = CustomerOrder::get()->count();
        return $this->respondSuccessData($sumCustomerOrders);
    }

}
