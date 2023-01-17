@extends('admin.layouts.app')

@section('title', 'Users')
@section('sub-title', 'Packages')
@section('content')
<div class="main-content">
      <div class="content-heading clearfix">

            <ul class="breadcrumb">
                  <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
                  <li><a href="{{url('admin/users')}}"><i class="fa fa-user"></i>Users</a></li>
                  <li>Packages</li>
            </ul>
      </div>
      <div class="container-fluid">
            @include('admin.messages')
            <div class="panel">
                  <div class="panel-heading">
                        <h3 class="panel-title">Packages</h3>
                        <div class="right">
                              <span class="label label-default" style="font-size: 90%;">{{$user->name.' - '.$user->email}}</span>
                        </div>
                  </div>
                  @if($pending_payments)
                  <div class="panel-heading bg-info">
                        <p> This user has pending payments in Pay As You Go package. If you wish to change the user's package, those payments will be cleared and the package will switch. </p>
                  </div>
                  @endif
                  <div class="panel-body">
                        <div class="row">
                              @foreach ($packages as $package)
                              <?php
                              $max = 25;
                              if ($package->id == $user->package_id) {
                                    if (!empty($user->subscription->type) && $user->subscription->type == 1) {
                                          if (!empty($user->subscription->payment_option && $user->subscription->payment_option == 2)) {
                                                $max = 12;
                                          }
                                    } else {
                                          if (!empty($user->subscription->payment_option && $user->subscription->payment_option == 1)) {
                                                $max = 5;
                                          } else {
                                                $max = 1;
                                          }
                                    }
                              }
                              ?>

                              @if( $loop->index %3 == 0 )
                              <div class="row">
                                    @endif


                                    <div class="col-sm-6 col-md-4">
                                          <form class="form-horizontal label-left" action="{{url('admin/users/update-package')}}" enctype="multipart/form-data" method="POST">
                                                @csrf

                                                <input name="user_id" type="hidden" value="{{ $user->id }}" />
                                                <input name="package_id" type="hidden" value="{{ $package->id }}" />
                                                <input id="current_subscription_type" type="hidden" value="{{ $user->subscription->type }}" />

                                                <div class="thumbnail package-thumbnail">
                                                      <div class="top-head">
                                                            <center>
                                                                  <h3>{{$package->title}}</h3>
                                                            </center>
                                                      </div>
                                                      <div class="caption package-single">
                                                            <!-- <div class="top-head">
											<center><h3>{{$package->title}}</h3></center>
										</div> -->
                                                            <!-- <hr> -->
                                                            <b>
                                                                  <span class="pull-left">
                                                                        {{ config('constants.currency')['symbol'].''. $package->monthly_price }} /
                                                                        Month
                                                                  </span>
                                                                  <span class="pull-right">
                                                                        {{ config('constants.currency')['symbol'].''. $package->yearly_price }} /
                                                                        Year
                                                                  </span>
                                                            </b>
                                                            <br>
                                                            <hr>
                                                            {!! $package->description !!}
                                                            <p>
                                                                  @if($package->id != 9)
                                                                  <label for="type" class="control-label">
                                                                        Select Type
                                                                  </label>
                                                                  <select class="form-control" name="type" data-for="type" onchange="handleTypeChange($(this),this.value,{{ $user->subscription->type }},{{$package->id == $user->package_id  ? 1 : 0}},{{$package->id}})">
                                                                        <option value="1" {{ $package->id == $user->package_id && $user->subscription->type == 1 ? "selected" : "" }}>
                                                                              Monthly
                                                                        </option>
                                                                        <option value="2" {{ $package->id == $user->package_id && $user->subscription->type == 2 ? "selected" : "" }}>
                                                                              Yearly
                                                                        </option>
                                                                  </select>

                                                                  <label for="repetition" class="control-label" style="opacity: {{$package->id == 2 ? 0 : 1}}">
                                                                        For number of Months / Years
                                                                  </label>
                                                                  <input onchange="handleNumberChange($(this),this.value,{{$package->id == $user->package_id  ? 1 : 0}},{{$package->id}}, {{$user->subscription->repetition}})" style="pointer-events: {{$package->id == 2 ? 'none' : 'auto'}};opacity: {{$package->id == 2 ? 0 : 1}}" class="form-control" type="number" name="repetition" value="{{$package->id == $user->package_id  ? $user->subscription->repetition : 1}}" min="1" max={{ $max }}>

                                                                  <label for="payment_option" class="control-label" style="opacity: {{$package->id == 2 ? 0 : 1}}">
                                                                        Select Payment
                                                                  </label>
                                                                  <select class="form-control payment" name="payment_option" data-for="payment" style="pointer-events: {{$package->id == 2 ? 'none' : 'auto'}};opacity: {{$package->id == 2 ? 0 : 1}}" onchange="handlePaymentOptionChange($(this),this.value,{{$package->id == $user->package_id  ? 1 : 0}},{{$package->id}}, {{$user->subscription->payment_option}})">
                                                                        <option value="1" {{ $package->id == $user->package_id && $user->subscription->payment_option == 1 ? "selected" : "" }}>
                                                                              Free
                                                                        </option>
                                                                        <option value="2" {{ $package->id == $user->package_id && $user->subscription->payment_option == 2 ? "selected" : "" }}>
                                                                              Paid
                                                                        </option>
                                                                  </select>
                                                                  @else
                                                                  <input type="hidden" name="type" value="1">
                                                                  <input type="hidden" name="repetition" value="1">
                                                                  <input type="hidden" name="payment_option" value="1">
                                                                  @endif
                                                            </p>
                                                            <center>
                                                                  @if($package->id == $user->package_id)
                                                                  <button id="current_btn_{{$package->id}}" class="btn btn-primary submit-btn" type="submit" {{ ($user->is_expired == 0) ? "disabled" : "" }}>Current</button>
                                                                  @elseif($package->id > $user->package_id)
                                                                  <button class="btn btn-primary submit-btn" type="submit">Upgrade</button>
                                                                  @else
                                                                  <button class="btn btn-primary submit-btn" type="submit">Downgrade</button>
                                                                  @endif
                                                            </center>
                                                      </div>
                                                </div>
                                          </form>
                                    </div>
                                    @if( $loop->index %3 == 2 )
                              </div>
                              @endif
                              @endforeach
                        </div>
                  </div>
            </div>
      </div>
</div>
@endsection

@section('js')
<script>
      function handleTypeChange(_context, type, current_subscription_type, is_current_package, package_id) {
            $(_context).val(type).prop("selected", true);
            var payment_option = $(_context).siblings('select[name="payment_option"]').val();
            console.log(type, is_current_package, current_subscription_type, package_id);

            if (is_current_package == 1) {
                  if (type != current_subscription_type) {
                        $('#current_btn_' + package_id).removeAttr('disabled');
                        $('#current_btn_' + package_id).text('Update');
                  } else {
                        $('#current_btn_' + package_id).prop("disabled", true);
                        $('#current_btn_' + package_id).text('Current');
                  }
            }

            if (type == 1) {
                  if (payment_option == 1) {
                        $(_context).siblings('input[name="repetition"]').attr('max', 25);
                  } else {
                        $(_context).siblings('input[name="repetition"]').attr('max', 12);
                  }
            } else {
                  if (payment_option == 1) {
                        $(_context).siblings('input[name ="repetition"]').attr('max', 5);
                  } else {
                        $(_context).siblings('input[name ="repetition"]').attr('max', 1);
                  }
            }
      }

      function handleNumberChange(_context, repetition, is_current_package, package_id, current_repetition) {
            $(_context).val(repetition);

            if (is_current_package == 1) {
                  if (repetition != current_repetition) {
                        $('#current_btn_' + package_id).removeAttr('disabled');
                        $('#current_btn_' + package_id).text('Update');
                  } else {
                        $('#current_btn_' + package_id).prop("disabled", true);
                        $('#current_btn_' + package_id).text('Current');
                  }
            }
      }

      function handlePaymentOptionChange(_context, payment_option, is_current_package, package_id, current_payment_option) {
            $(_context).val(payment_option).prop("selected", true);
            var type = $(_context).siblings('select[name="type"]').val();
            console.log(payment_option, is_current_package, package_id, current_payment_option);

            if (is_current_package == 1) {
                  if (payment_option != current_payment_option) {
                        $('#current_btn_' + package_id).removeAttr('disabled');
                        $('#current_btn_' + package_id).text('Update');
                  } else {
                        $('#current_btn_' + package_id).prop("disabled", true);
                        $('#current_btn_' + package_id).text('Current');
                  }
            }

            if (type == 1) {
                  if (payment_option == 1) {
                        $(_context).siblings('input[name="repetition"]').attr('max', 25);
                  } else {
                        $(_context).siblings('input[name="repetition"]').attr('max', 12);
                  }
            } else {
                  if (payment_option == 1) {
                        $(_context).siblings('input[name="repetition"]').attr('max', 5);
                  } else {
                        $(_context).siblings('input[name="repetition"]').attr('max', 1);
                  }
            }
      }

      $('.submit-btn').click(function() {
            var repetition = $(this).parent().siblings().find('input[name="repetition"]').val();
            var max_allowed = $(this).parent().siblings().find('input[name="repetition"]').attr('max');
            var payment_option = $(this).parent().siblings().find('select[name="payment_option"]').val();

            if (payment_option != 1 && (Number(repetition) > Number(max_allowed))) {
                  alert('Your request cannot be processed! Payment Gateway does not allow setting recessive payments for more than 12 months (1 year). Please adjust the time duration and try again.')
                  return false;
            }
      });
</script>
@endsection