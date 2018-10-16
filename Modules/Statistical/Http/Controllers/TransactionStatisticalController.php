<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 06/06/2018
 * Time: 2:03 CH
 */

namespace Modules\Statistical\Http\Controllers;

use DateTime;
use DB;
use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Statistical\Models\Statistical;
use Modules\Transaction\Models\Transaction;

class TransactionStatisticalController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getListSumTransaction(Request $request)
    {
        $this->authorize('index', Statistical::class);
        $keyGroupBy = $request->input('group_by');

        $sumListSumTransaction = collect();
        $output = collect();
        switch ($keyGroupBy) {
            case 'day' :
                $startDate = is_null($request->input('created_at_from')) ? date('Y') . '/' . date('m') . '/01 00:00:00' : $request->input('created_at_from');
                $endDate = is_null($request->input('created_at_to')) ? date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59' : $request->input('created_at_to');

                $sumListSumTransaction = Transaction::leftJoin('customer_orders', 'transactions.transactiontable_id', '=', 'customer_orders.id')
                    ->leftJoin('bill_of_ladings', 'transactions.transactiontable_id', '=', 'bill_of_ladings.id')
                    ->where(function ($q) use ($request) {
                        $q->filterWhere('bill_of_ladings.seller_id', '=', $request->input('seller_id'))
                            ->filterOrWhere('customer_orders.seller_id', '=', $request->input('seller_id'));
                    })
                    ->where(function ($q) {
                        $q->where('transactions.transactiontable_type', '=', CustomerOrder::class)
                            ->orWhere('transactions.transactiontable_type', '=', BillOfLading::class);
                    })
                    ->filterWhere('transactions.created_at', '>=', $startDate)
                    ->filterWhere('transactions.created_at', '<=', $endDate)
                    ->select(DB::raw('sum(money) as sum'),
                        DB::raw('count(transactions.id) as count'),
                        DB::raw('YEAR(transactions.created_at) year, MONTH(transactions.created_at) month, DAY(transactions.created_at) day'))
                    ->groupBy('year', 'month', 'day')
                    ->orderBy('year', 'month', 'day')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%a');

                $currentDate = str_replace('-', '/', $startDate);

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'd/m/Y');

                    foreach ($sumListSumTransaction as $item) {
                        $itemDay = $item->day < 10 ? '0' . $item->day : $item->day;
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/' . $itemDay;

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum, 'count' => $item->count]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0, 'count' => 0]);
                    }
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 day'));
                }

                break;
            case 'month' :
                $startDate = is_null($request->input('from_month')) ? date('Y') . '/01/01 00:00:00' : $request->input('from_month') . '/01 00:00:00';
                $endDate = is_null($request->input('to_month'))
                    ? date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59'
                    : $request->input('to_month') . '/' . date('t') . ' 23:59:59';

                $sumListSumTransaction = Transaction::leftJoin('customer_orders', 'transactions.transactiontable_id', '=', 'customer_orders.id')
                    ->leftJoin('bill_of_ladings', 'transactions.transactiontable_id', '=', 'bill_of_ladings.id')
                    ->where(function ($q) use ($request) {
                        $q->filterWhere('bill_of_ladings.seller_id', '=', $request->input('seller_id'))
                            ->filterOrWhere('customer_orders.seller_id', '=', $request->input('seller_id'));
                    })
                    ->where(function ($q) {
                        $q->where('transactions.transactiontable_type', '=', CustomerOrder::class)
                            ->orWhere('transactions.transactiontable_type', '=', BillOfLading::class);
                    })
                    ->filterWhere('transactions.created_at', '>=', $startDate)
                    ->filterWhere('transactions.created_at', '<=', $endDate)
                    ->select(DB::raw('sum(money) as sum'),
                        DB::raw('count(transactions.id) as count'),
                        DB::raw('YEAR(transactions.created_at) year, MONTH(transactions.created_at) month'))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'month')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%m');

                $currentDate = str_replace('-', '/', $startDate);
                $currentDate = date('Y/m/d', strtotime($currentDate . ' -1 month')); // convert

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 month'));
                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'm/Y');

                    foreach ($sumListSumTransaction as $item) {
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum, 'count' => $item->count]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0, 'count' => 0]);
                    }
                }

                break;
            case 'year' :
                $startDate = is_null($request->input('from_year')) ? null : $request->input('from_year') . '/01/01 00:00:00';
                $endDate = is_null($request->input('to_year')) ? null : $request->input('to_year') . '/12/31 23:59:59';

                $sumListSumTransaction = Transaction::leftJoin('customer_orders', 'transactions.transactiontable_id', '=', 'customer_orders.id')
                    ->leftJoin('bill_of_ladings', 'transactions.transactiontable_id', '=', 'bill_of_ladings.id')
                    ->where(function ($q) use ($request) {
                        $q->filterWhere('bill_of_ladings.seller_id', '=', $request->input('seller_id'))
                            ->filterOrWhere('customer_orders.seller_id', '=', $request->input('seller_id'));
                    })
                    ->where(function ($q) {
                        $q->where('transactions.transactiontable_type', '=', CustomerOrder::class)
                            ->orWhere('transactions.transactiontable_type', '=', BillOfLading::class);
                    })
                    ->filterWhere('customer_orders.created_at', '>=', $startDate)
                    ->filterWhere('transactions.created_at', '<=', $endDate)
                    ->select(DB::raw('sum(money) as sum'),
                        DB::raw('count(transactions.id) as count'),
                        DB::raw('YEAR(transactions.created_at) year'))
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%y');

                $currentDate = str_replace('-', '/', $startDate);
                $currentDate = date('Y/m/d', strtotime($currentDate . ' -1 year')); // convert

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 year'));
                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'Y');

                    foreach ($sumListSumTransaction as $item) {
                        $dataDate = $item->year . '/01/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum, 'count' => $item->count]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0, 'count' => 0]);
                    }
                }

                break;

                break;
            default :
                $startDate = date('Y') . '/01/01 00:00:00';
                $endDate = date('Y') . '/' . date('m') . '/' . date('t') . ' 23:59:59';

                $sumListSumTransaction = Transaction::leftJoin('customer_orders', 'transactions.transactiontable_id', '=', 'customer_orders.id')
                    ->leftJoin('bill_of_ladings', 'transactions.transactiontable_id', '=', 'bill_of_ladings.id')
                    ->where(function ($q) use ($request) {
                        $q->filterWhere('bill_of_ladings.seller_id', '=', $request->input('seller_id'))
                            ->filterOrWhere('customer_orders.seller_id', '=', $request->input('seller_id'));
                    })
                    ->where(function ($q) {
                        $q->where('transactions.transactiontable_type', '=', CustomerOrder::class)
                            ->orWhere('transactions.transactiontable_type', '=', BillOfLading::class);
                    })
                    ->filterWhere('transactions.created_at', '>=', $startDate)
                    ->filterWhere('transactions.created_at', '<=', $endDate)
                    ->select(DB::raw('sum(money) as sum'),
                        DB::raw('count(transactions.id) as count'),
                        DB::raw('YEAR(transactions.created_at) year, MONTH(transactions.created_at) month'))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'month')
                    ->get();

                $endDate = new DateTime(date($endDate));
                $startDate2 = new DateTime(date($startDate));

                $dateDiff = $endDate->diff($startDate2)->format('%m');

                $currentDate = str_replace('-', '/', $startDate);
                $currentDate = date('Y/m/d', strtotime($currentDate . ' -1 month')); // convert

                for ($i = 0; $i <= $dateDiff; $i++) {
                    $currentDate = date('Y/m/d', strtotime($currentDate . ' +1 month'));
                    $flag = true;
                    $displayDate = new DateTime($currentDate);
                    $displayDate = date_format($displayDate, 'm/Y');

                    foreach ($sumListSumTransaction as $item) {
                        $itemMonth = $item->month < 10 ? '0' . $item->month : $item->month;
                        $dataDate = $item->year . '/' . $itemMonth . '/01';

                        if ($dataDate == $currentDate) {
                            $output->push(['date' => $displayDate, 'sum' => $item->sum, 'count' => $item->count]);
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $output->push(['date' => $displayDate, 'sum' => 0, 'count' => 0]);
                    }
                }

                break;
        }

        return $this->respondSuccessData($output);
    }
}
