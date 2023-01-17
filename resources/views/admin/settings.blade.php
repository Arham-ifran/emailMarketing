@extends('admin.layouts.app')

@section('title', 'Settings')
@section('sub-title', 'Site Settings')

@section('content')
<div class="main-content">
  <div class="content-heading clearfix">

    <ul class="breadcrumb">
      <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
      <li>Settings</li>
    </ul>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel">
          <div class="panel-heading">
            <h3 class="panel-title">Settings</h3>
          </div>
          <div class="panel-body">
            @include('admin.messages')
            <form id="settings-form" class="form-horizontal label-left" action="{{url('admin/settings')}}" enctype="multipart/form-data" method="POST">
              {{ csrf_field() }}

              <div class="form-group">
                <label class="col-sm-3 control-label">Site Title</label>
                <div class="col-sm-9">
                  <input type="text" name="site_title" maxlength="200" class="form-control" value="{{isset($settings['site_title']) ? $settings['site_title'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Office Address</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="office_address" maxlength="1000" rows="5" style="resize: none" required="">{{isset($settings['office_address']) ? $settings['office_address'] : ''}}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Contact Number</label>
                <div class="col-sm-9">
                  <input type="text" name="contact_number" maxlength="50" class="form-control" value="{{isset($settings['contact_number']) ? $settings['contact_number'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Contact Email</label>
                <div class="col-sm-9">
                  <input type="email" name="contact_email" maxlength="200" class="form-control" value="{{isset($settings['contact_email']) ? $settings['contact_email'] : ''}}" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Company Name</label>
                <div class="col-sm-9">
                  <input type="text" name="company_name" maxlength="50" class="form-control" value="{{isset($settings['company_name']) ? $settings['company_name'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Company Registration Number</label>
                <div class="col-sm-9">
                  <input type="text" name="company_registration_number" maxlength="50" class="form-control" value="{{isset($settings['company_registration_number']) ? $settings['company_registration_number'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Company Website</label>
                <div class="col-sm-9">
                  <input type="url" name="website" maxlength="200" class="form-control" value="{{isset($settings['website']) ? $settings['website'] : ''}}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Commercial Register Address</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="commercial_register_address" maxlength="1000" rows="5" style="resize: none" required="">{{isset($settings['commercial_register_address']) ? $settings['commercial_register_address'] : ''}}</textarea>
                </div>
              </div>

              <hr>

              <div class="form-group">
                <label class="col-sm-3 control-label">Financial Details BCC Emails (Separte with Commas)</label>
                <div class="col-sm-9">
                  <input type="text" name="bcc_emails" maxlength="500" class="form-control" value="{{isset($settings['bcc_emails']) ? $settings['bcc_emails'] : ''}}" required>
                </div>
              </div>

              <!-- <hr>
              <h4 class="heading">Mailgun Settings</h4>
              <div class="form-group">
                <label class="col-sm-3 control-label">Mailgun Username</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="mailgun_username" maxlength="1000" rows="2" style="resize: none" required="">{{isset($settings['mailgun_username']) ? $settings['mailgun_username'] : ''}}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Mailgun Password</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="mailgun_password" maxlength="1000" rows="2" style="resize: none" required="">{{isset($settings['mailgun_password']) ? $settings['mailgun_password'] : ''}}</textarea>
                </div>
              </div> -->
              <!-- 
              <div class="form-group">
                <label class="col-sm-3 control-label">Mail From Email Address</label>
                <div class="col-sm-9">
                  <input type="text" name="mail_from_address" maxlength="50" class="form-control" value="{{isset($settings['mail_from_address']) ? $settings['mail_from_address'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Mail From App Name</label>
                <div class="col-sm-9">
                  <input type="text" name="mail_from_app_name" maxlength="50" class="form-control" value="{{isset($settings['mail_from_app_name']) ? $settings['mail_from_app_name'] : ''}}" required>
                </div>
              </div> -->

              <hr>
              <h4 class="heading">Twilio Settings</h4>
              <div class="form-group">
                <label class="col-sm-3 control-label">Twilio SID</label>
                <div class="col-sm-9">
                  <input type="text" name="twilio_sid" maxlength="50" class="form-control" value="{{isset($settings['twilio_sid']) ? $settings['twilio_sid'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Twilio Auth Token</label>
                <div class="col-sm-9">
                  <input type="text" name="twilio_auth_token" maxlength="50" class="form-control" value="{{isset($settings['twilio_auth_token']) ? $settings['twilio_auth_token'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Twilio Number</label>
                <div class="col-sm-9">
                  <input type="text" name="twilio_number" maxlength="50" class="form-control" value="{{isset($settings['twilio_number']) ? $settings['twilio_number'] : ''}}" required>
                </div>
              </div>

              <!-- <hr>
              <h4 class="heading">IMAP Settings</h4>
              <div class="form-group">
                <label class="col-sm-3 control-label">IMAP Username</label>
                <div class="col-sm-9">
                  <input type="text" name="imap_username" maxlength="50" class="form-control" value="{{isset($settings['imap_username']) ? $settings['imap_username'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Imap Password</label>
                <div class="col-sm-9">
                  <input type="text" name="imap_password" maxlength="50" class="form-control" value="{{isset($settings['imap_password']) ? $settings['imap_password'] : ''}}" required>
                </div>
              </div> -->

              <!-- <hr>
              <h4 class="heading">Company Address Information</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Street</label>
                <div class="col-sm-9">
                  <input type="text" name="company_street" maxlength="50" class="form-control" value="{{isset($settings['company_street']) ? $settings['company_street'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Zip Code</label>
                <div class="col-sm-9">
                  <input type="text" name="company_zip_code" maxlength="50" class="form-control" value="{{isset($settings['company_zip_code']) ? $settings['company_zip_code'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">City</label>
                <div class="col-sm-9">
                  <input type="text" name="company_city" maxlength="50" class="form-control" value="{{isset($settings['company_city']) ? $settings['company_city'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Country</label>
                <div class="col-sm-9">
                  <input type="text" name="company_country" maxlength="50" class="form-control" value="{{isset($settings['company_country']) ? $settings['company_country'] : ''}}" required>
                </div>
              </div> -->
              <!--<hr>
               <h4 class="heading">Bank Information</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Bank Name</label>
                <div class="col-sm-9">
                  <input type="text" name="bank_name" maxlength="50" class="form-control" value="{{isset($settings['bank_name']) ? $settings['bank_name'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">IBAN</label>
                <div class="col-sm-9">
                  <input type="text" name="iban" maxlength="50" class="form-control" value="{{isset($settings['iban']) ? $settings['iban'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Code</label>
                <div class="col-sm-9">
                  <input type="text" name="code" maxlength="50" class="form-control" value="{{isset($settings['code']) ? $settings['code'] : ''}}" required>
                </div>
              </div> -->
              <hr>
              <h4 class="heading">Social Media Links</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Instagram</label>
                <div class="col-sm-9">
                  <input type="url" name="instagram" maxlength="200" class="form-control" value="{{isset($settings['instagram']) ? $settings['instagram'] : ''}}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Facebook</label>
                <div class="col-sm-9">
                  <input type="url" name="facebook" maxlength="200" class="form-control" value="{{isset($settings['facebook']) ? $settings['facebook'] : ''}}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Twitter</label>
                <div class="col-sm-9">
                  <input type="url" name="twitter" maxlength="200" class="form-control" value="{{isset($settings['twitter']) ? $settings['twitter'] : ''}}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">LinkedIn</label>
                <div class="col-sm-9">
                  <input type="url" name="linkedin" maxlength="200" class="form-control" value="{{isset($settings['linkedin']) ? $settings['linkedin'] : ''}}">
                </div>
              </div>

              <hr>
              <h4 class="heading">Payment Relief Settings</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Pending payment relief number of days</label>
                <div class="col-sm-9">
                  <input type="number" name="payment_relief_days" min="1" class="form-control" value="{{isset($settings['payment_relief_days']) ? $settings['payment_relief_days'] : ''}}">
                </div>
              </div>

              <!-- <div class="form-group">
                <label class="col-sm-3 control-label">Voucher Expiry (In Months)</label>
                <div class="col-sm-9">
                  <input type="number" name="voucher_expiry" min="0" class="form-control" value="{{isset($settings['voucher_expiry']) ? $settings['voucher_expiry'] : ''}}">
                </div>
              </div> -->

              <hr>
              <h4 class="heading">VAT Settings</h4>
              <div class="form-group">
                <label class="col-sm-3 control-label">VAT ID</label>
                <div class="col-sm-9">
                  <input type="text" name="vat_id" maxlength="50" class="form-control" value="{{isset($settings['vat_id']) ? $settings['vat_id'] : ''}}" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">VAT (%)</label>
                <div class="col-sm-9">
                  <input type="number" name="vat" min="0" max="100" class="form-control" value="{{isset($settings['vat']) ? $settings['vat'] : ''}}">
                </div>
              </div>

              <hr>
              <h4 class="heading">User Iactivity</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Make user inactive after number of days</label>
                <div class="col-sm-9">
                  <input type="number" name="user_inactivation_days" min="0" placeholder="30" class="form-control" value="{{isset($settings['user_inactivation_days']) ? $settings['user_inactivation_days'] : ''}}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">Soft Delete user after number of days</label>
                <div class="col-sm-9">
                  <input type="number" name="user_soft_deletion_days" placeholder="15" min="0" class="form-control" value="{{isset($settings['user_soft_deletion_days']) ? $settings['user_soft_deletion_days'] : ''}}">
                </div>
              </div>

              <hr>
              <h4 class="heading">Account Inactivity Follow-ups</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">First Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="first_notification" placeholder="5" min="0" class="form-control" value="{{isset($settings['first_notification']) ? $settings['first_notification'] : ''}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Second Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="second_notification" placeholder="3" min="0" class="form-control" value="{{isset($settings['second_notification']) ? $settings['second_notification'] : ''}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Third Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="third_notification" placeholder="1" min="0" class="form-control" value="{{isset($settings['third_notification']) ? $settings['third_notification'] : ''}}">
                </div>
              </div>

              <hr>
              <h4 class="heading">User Deletion Settings</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">Delete user after number of days</label>
                <div class="col-sm-9">
                  <input type="number" name="user_deletion_days" min="0" placeholder="15" class="form-control" value="{{isset($settings['user_deletion_days']) ? $settings['user_deletion_days'] : ''}}">
                </div>
              </div>

              <hr>
              <h4 class="heading">Subscription Expiry Notifications</h4>

              <div class="form-group">
                <label class="col-sm-3 control-label">First Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="subscription_expiry_first_notification" placeholder="5" min="0" class="form-control" value="{{isset($settings['subscription_expiry_first_notification']) ? $settings['subscription_expiry_first_notification'] : ''}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Second Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="subscription_expiry_second_notification" placeholder="3" min="0" class="form-control" value="{{isset($settings['subscription_expiry_second_notification']) ? $settings['subscription_expiry_second_notification'] : ''}}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Third Notification (In days)</label>
                <div class="col-sm-9">
                  <input type="number" name="subscription_expiry_third_notification" placeholder="1" min="0" class="form-control" value="{{isset($settings['subscription_expiry_third_notification']) ? $settings['subscription_expiry_third_notification'] : ''}}">
                </div>
              </div>

              <div class="text-right">
                <a href="{{url('admin')}}">
                  <button type="button" class="btn cancel btn-fullrounded">
                    <span>Cancel</span>
                  </button>
                </a>

                <button type="submit" class="btn btn-primary btn-fullrounded">
                  <span>Save</span>
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  $(function() {
    $('#settings-form').validate({
      errorElement: 'div',
      errorClass: 'help-block',
      focusInvalid: true,

      highlight: function(e) {
        $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
      },
      success: function(e) {
        $(e).closest('.form-group').removeClass('has-error');
        $(e).remove();
      },
      errorPlacement: function(error, element) {
        if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
          var controls = element.closest('div[class*="col-"]');
          if (controls.find(':checkbox,:radio').length > 1)
            controls.append(error);
          else
            error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
        } else if (element.is('.select2')) {
          error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
        } else if (element.is('.chosen-select')) {
          error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
        } else
          error.insertAfter(element);
      },
      invalidHandler: function(form, validator) {
        $('html, body').animate({
          scrollTop: $(validator.errorList[0].element).offset().top - scrollTopDifference
        }, 500);
      },
      submitHandler: function(form, validator) {
        if ($(validator.errorList).length == 0) {
          document.getElementById("page-overlay").style.display = "block";
          return true;
        }
      }
    });
  });
</script>
@endsection