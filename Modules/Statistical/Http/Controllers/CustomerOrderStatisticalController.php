<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 24/05/2018
 * Time: 2:22 CH
 */

namespace Modules\Statistical\Http\Controllers;

use DateTime;
use DB;
use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Statistical\Models\Statistical;
use Modules\User\Models\User;

class CustomerOrderStatisticalController extends Controller
{
    // Admin, CustomerServiceManagement and CustomerServiceOfficer can call api in this controller

    /**
     * CustomerServiceOfficer - It will return sum of their CustomerOrders
     * Admin and CustomerServiceManagement - get all
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getSumCustomerOrder()
    {
        $this->authorize('index', Statistical::class);

        /** @var User $user */
        $user = auth()->user();

        $sumCustomerOrders = CustomerOrder::where(function ($query) use ($user) {
            if (!is_null(auth()->user())) {
                $sellerID = $user->isCustomerServiceOfficer() ? $user->id : null;
            } else {
                $sellerID = null;
            }

            $query->filterWhere('seller_id', '=', $sellerID);
        })->get()->count();

        return $this->respondSuccessData($sumCustomerOrders);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getSumCustomerOrderFinished()
    {
        $this->authorize('index', Statistical::class);
        /** @var User $user */
        $user = auth()->user();

        $sumCustomerOrdersFinished = CustomerOrder::where(function ($query) use ($user) {
            $sellerID = auth()->user()->isCustomerServiceOfficer() ? $user->id : null;
            $query->filterWhere('seller_id', '=', $sellerID)->filterWhere('status', '=', CustomerOrder::STATUS_FINISHED);
        })->get()->count();

        return $this->respondSuccessData($sumCustomerOrdersFinished);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getListSumCustomerOrderByUser(Request $request)
    {
        $this->authorize('index', Statistical::class);

        $sumListSumCustomerOrderByUser = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $request->input('created_at_from'))
            ->filterWhere('customer_orders.created_at', '<=', $request->input('created_at_to'))
            ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
            ->select('seller_id', DB::raw('count(*) as sum'))
            ->groupBy('seller_id')
            ->get();

        return $this->respondSuccessData($sumListSumCustomerOrderByUser);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getListSumCustomerOrderOneYear(Request $request)
    {
        $this->authorize('index', Statistical::class);

        $keyGroupBy = $request->input('group_by');
        $sumListSumCustomerOrderByOneYear = collect();

        if ($keyGroupBy == 'day') {
            $start_date = is_null($request->input('created_at_from')) ? date('Y') . '/' . date('m') . '/01 00:00:00' : $request->input('created_at_from');
            $end_date = is_null($request->input('created_at_to')) ? date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59' : $request->input('created_at_to');

            $sumListSumCustomerOrderByOneYear = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $start_date)
                ->filterWhere('customer_orders.created_at', '<=', $end_date)
                ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                ->filterWhere('customer_orders.status', '=', $request->input('status'))
                ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day'))
                ->groupBy('year', 'month', 'day')
                ->orderBy('year', 'month', 'day')
                ->get();
        } elseif ($keyGroupBy == 'year') {
            $sumListSumCustomerOrderByOneYear = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $request->input('created_at_from'))
                ->filterWhere('customer_orders.created_at', '<=', $request->input('created_at_to'))
                ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                ->filterWhere('customer_orders.status', '=', $request->input('status'))
                ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year'))
                ->groupBy('year')
                ->orderBy('year')
                ->get();
        } else {
            $start_date = is_null($request->input('created_at_from')) ? date('Y') . '/01/01 00:00:00' : $request->input('created_at_from');
            $end_date = is_null($request->input('created_at_to')) ? date('Y') . '/12/31 23:59:59' : $request->input('created_at_to');

            $sumListSumCustomerOrderByOneYear = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $start_date)
                ->filterWhere('customer_orders.created_at', '<=', $end_date)
                ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                ->filterWhere('customer_orders.status', '=', $request->input('status'))
                ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupBy('year', 'month')
                ->orderBy('year', 'month')
                ->get();
        }

        $output = collect();

        if ($keyGroupBy == 'day') {
            foreach ($sumListSumCustomerOrderByOneYear as $item) {
                $item->day = $item->day < 10 ? '0' . $item->day : $item->day;
                $item->month = $item->month < 10 ? '0' . $item->month : $item->month;
                $output->push(['date' => $item->day . '/' . $item->month . '/' . $item->year, 'sum' => $item->sum]);
            }
        } else {
            foreach ($sumListSumCustomerOrderByOneYear as $item) {
                $item->month = $item->month < 10 ? '0' . $item->month : $item->month;
                $output->push(['date' => $item->month . '/' . $item->year, 'sum' => $item->sum]);
            }
        }

        return $this->respondSuccessData($output);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getListSumCustomerOrder(Request $request)
    {
        $this->authorize('index', Statistical::class);

        $keyGroupBy = $request->input('group_by');

        $sumListSumCustomerOrder = collect();
        $output = collect();
        switch ($keyGroupBy) {
            case 'day' :
                $startDate = is_null($request->input('created_at_from')) ? date('Y') . '/' . date('m') . '/01 00:00:00' : $request->input('created_at_from');
                $endDate = is_null($request->input('created_at_to')) ? date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59' : $request->input('created_at_to');

                $sumListSumCustomerOrder = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $startDate)
                    ->filterWhere('customer_orders.created_at', '<=', $endDate)
                    ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                    ->filterWhere('customer_orders.status', '=', $request->input('status'))
                    ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day'))
                    ->groupBy('year', 'month', 'day')
                    ->orderBy('year', 'month', 'day')
                    ->get();


                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%a');

                $currentDate = $startDate;

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'd/m/Y');

                    foreach ($sumListSumCustomerOrder as $item) {
                        $itemDay = $item->day < 10 ? '0' . $item->day : $item->day;
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/' . $itemDay;

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0]);
                    }
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 day'));
                }

                break;
            case 'month' :
                $startDate = is_null($request->input('from_month')) ? date('Y') . '/01/01 00:00:00' : $request->input('from_month') . '/01 00:00:00';
                $endDate = is_null($request->input('to_month'))
                    ? date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59'
                    : $request->input('to_month') . '/' . date('t') . ' 23:59:59';

                $sumListSumCustomerOrder = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $startDate)
                    ->filterWhere('customer_orders.created_at', '<=', $endDate)
                    ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                    ->filterWhere('customer_orders.status', '=', $request->input('status'))
                    ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'month')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%m');

                $currentDate = date('Y/m/d', strtotime($startDate . ' -1 month')); // format date :(((

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 month'));

                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'm/Y');

                    foreach ($sumListSumCustomerOrder as $item) {
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0]);
                    }
                }

                break;
            case 'year' :
                $startDate = is_null($request->input('from_year')) ? null : $request->input('from_year') . '/01/01 00:00:00';
                $endDate = is_null($request->input('to_year')) ? null : $request->input('to_year') . '/12/31 23:59:59';

                $sumListSumCustomerOrder = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $startDate)
                    ->filterWhere('customer_orders.created_at', '<=', $endDate)
                    ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                    ->filterWhere('customer_orders.status', '=', $request->input('status'))
                    ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year'))
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get();


                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%y');

                $currentDate = date('Y/m/d', strtotime($startDate . ' -1 year')); // format date :(((

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 year'));

                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'Y');

                    foreach ($sumListSumCustomerOrder as $item) {
                        $dataDate = $item->year . '/01/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0]);
                    }
                }

                break;
            default :
                $startDate = date('Y') . '/01/01 00:00:00';
                $endDate = date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59';

                $sumListSumCustomerOrder = CustomerOrder::filterWhere('customer_orders.created_at', '>=', $startDate)
                    ->filterWhere('customer_orders.created_at', '<=', $endDate)
                    ->filterWhere('customer_orders.seller_id', '=', $request->input('seller_id'))
                    ->filterWhere('customer_orders.status', '=', $request->input('status'))
                    ->select(DB::raw('count(id) as sum'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'month')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%m');

                $currentDate = date('Y/m/d', strtotime($startDate . ' -1 month')); // format date :(((

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 month'));

                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'm/Y');

                    foreach ($sumListSumCustomerOrder as $item) {
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0]);
                    }
                }

                break;
        }

        return $this->respondSuccessData($output);
    }
}