<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VoucherController extends Controller
{
  /**
   * Create a new VoucherController instance.
   *
   * @return void
   */

  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['redeem']]);
  }

  /**
   * Redeem Voucher of Product Immunity
   *
   * @return \Illuminate\Http\JsonResponse
   */

  public function redeem(Request $request)
  {
    $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';

    $data = array(
      'voucher' => $request->voucher,
      'platform' => $request->platform,
      'apply_voucher' => $request->apply_voucher,
      'lang' => $lang
    );

    $product_immunity_url = \Config::get('constants.product_immunity_url') . "/api/vouchers/redeem?lang=" . $lang;
    $response = checkVoucherValidity($product_immunity_url, $data);

    // Start- Voucher Integration with Odoo

    if (empty($response) || !array_key_exists('status', $response) || !$response['status']) {
      $odoo_timmunity_url = \Config::get('constants.odoo_timmunity_url') . "/api/redeem-voucher?lang=" . $lang;
      $response = checkVoucherValidity($odoo_timmunity_url, $data);
    }

    // End- Voucher Integration with Odoo

    return $response;
  }
}
