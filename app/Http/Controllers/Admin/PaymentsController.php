<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Payment;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\PayAsYouGoPayments;
use App\Models\User;
use Session;
use Hashids;
use Auth;
use DataTables;

class PaymentsController extends Controller
{
	public function index()
	{
		if (!have_right(130))
			access_denied();
		$data['model'] = PaymentGatewaySetting::first();
		return view('admin.payment_gateway_settings')->with($data);
	}

	public function update(Request $request)
	{
		$model = PaymentGatewaySetting::first();
		$model->fill($request->input());
		$model->save();

		Session::flash('flash_success', 'Payment Gateway Settings has been updated successfully.');
		return redirect()->back();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function payments(Request $request)
	{
		if (!have_right(131))
			access_denied();

		$data = [];
		if ($request->ajax()) {
			$db_record = Payment::orderBy('created_at', 'DESC'); //->get();
			if ($request->has('userid') && $request->userid != "") {
				$userid = Hashids::decode($request->userid);
				if ($userid[0])
					$db_record = $db_record->where('user_id', $userid[0]);
			}
			$datatable = Datatables::of($db_record);
			$datatable = $datatable->addIndexColumn();
			$datatable = $datatable->editColumn('status', function ($row) {
				$status = '<span class="label label-danger">UnPaid</span>';
				if ($row->status == 1) {
					$status = '<span class="label label-success">Paid</span>';
				}
				return $status;
			});
			$datatable = $datatable->addColumn('user', function ($row) {
				$user = User::where('id', $row->user_id)->first();
				if ($user) {
					$user = $user->email;
				} else {
					$user = '';
				}
				return $user;
			});
			$datatable = $datatable->addColumn('amount', function ($row) {
				return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->amount;
			});
			$datatable = $datatable->addColumn('total_amount', function ($row) {
				return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->total_amount;
			});
			$datatable = $datatable->addColumn('discount_amount', function ($row) {
				if ($row->discount_amount)
					return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->discount_amount;
				else
					return 0;
			});

			$datatable = $datatable->rawColumns(['status', 'action', 'amount', 'total_amount', 'discount_amount']);
			$datatable = $datatable->make(true);
			return $datatable;
		}
		return view('admin.payments.package', $data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function payAsYouGoPayments(Request $request)
	{
		if (!have_right(132))
			access_denied();

		$data = [];
		if ($request->ajax()) {
			$db_record = PayAsYouGoPayments::orderBy('created_at', 'DESC'); //->get();
			if ($request->has('userid') && $request->userid != "") {
				$userid = Hashids::decode($request->userid);
				if ($userid[0])
					$db_record = $db_record->where('user_id', $userid[0]);
			}
			$datatable = Datatables::of($db_record);
			$datatable = $datatable->addIndexColumn();
			$datatable = $datatable->editColumn('status', function ($row) {
				$status = '<span class="label label-danger">UnPaid</span>';
				if ($row->status == 1) {
					$status = '<span class="label label-success">Paid</span>';
				}
				return $status;
			});
			$datatable = $datatable->addColumn('user', function ($row) {
				$user = User::where('id', $row->user_id)->first();
				if ($user) {
					$user = $user->email;
				} else {
					$user = '';
				}
				return $user;
			});

			$datatable = $datatable->rawColumns(['status', 'action']);
			$datatable = $datatable->make(true);
			return $datatable;
		}
		return view('admin.payments.payasyougo-package', $data);
	}
}
