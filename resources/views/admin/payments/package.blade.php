@extends('admin.layouts.app')

@section('title', 'Package Payments')
@section('sub-title', 'Listing')
@section('content')
<div class="main-content">
      <div class="content-heading clearfix">

            <ul class="breadcrumb">
                  <li><a href="{{url('admin/dashboard')}}"><i class="fa fa-home"></i> Home</a></li>
                  <li>Package Payments</li>
            </ul>
      </div>
      <div class="container-fluid">
            @include('admin.messages')
            <!-- DATATABLE -->
            <div class="panel">
                  <div class="panel-heading">
                        <h3 class="panel-title">Package Payments</h3>
                  </div>
                  <div class="panel-body">
                        <table id="about-us-testimonials-datatable" class="table table-hover " style="width:100%">
                              <thead>
                                    <tr>
                                          <th>#</th>
                                          <th>User email</th>
                                          <th>Name</th>
                                          <th>Amount</th>
                                          <th>Voucher</th>
                                          <th>Discount Parcentage</th>
                                          <th>Discount Amount</th>
                                          <th>VAT%</th>
                                          <th>VAT amount</th>
                                          <th>Total amount</th>
                                          <th>Status</th>
                                    </tr>
                              </thead>
                              <tbody>
                              </tbody>
                        </table>
                  </div>
            </div>
            <!-- END DATATABLE -->
      </div>
</div>
@endsection

@section('js')
<script>
      $(function() {

            function checkname() {
                  let params = new URLSearchParams(location.search);
                  if (params.get('user')) {
                        return params.get('user');
                  }
                  return '';
            }
            const userid = checkname();

            $('#about-us-testimonials-datatable').dataTable({
                  pageLength: 50,
                  scrollX: true,
                  processing: false,
                  language: {
                        "processing": showOverlayLoader()
                  },
                  drawCallback: function() {
                        hideOverlayLoader();
                  },
                  responsive: true,
                  // dom: 'Bfrtip',
                  lengthMenu: [
                        [5, 10, 25, 50, 100, 200, -1],
                        [5, 10, 25, 50, 100, 200, "All"]
                  ],
                  serverSide: true,
                  ajax: {
                        url: '{{url("admin/package-payments")}}',
                        data: function(d) {
                              d.userid = userid;
                        }
                  },
                  columns: [{
                              data: 'DT_RowIndex',
                              name: 'DT_RowIndex',
                              orderable: false,
                              searchable: false
                        },
                        {
                              data: 'user',
                              name: 'user'
                        },
                        {
                              data: 'item',
                              name: 'item'
                        },
                        {
                              data: 'amount',
                              name: 'amount'
                        },
                        {
                              data: 'voucher',
                              name: 'voucher'
                        },
                        {
                              data: 'discount_percentage',
                              name: 'discount_percentage'
                        },
                        {
                              data: 'discount_amount',
                              name: 'discount_amount'
                        },
                        {
                              data: 'vat_percentage',
                              name: 'vat_percentage'
                        },
                        {
                              data: 'vat_amount',
                              name: 'vat_amount'
                        },
                        {
                              data: 'total_amount',
                              name: 'total_amount'
                        },
                        {
                              data: 'status',
                              name: 'status'
                        },
                  ]
            }).on('length.dt', function() {
                  showOverlayLoader();
            }).on('page.dt', function() {
                  showOverlayLoader();
            }).on('order.dt', function() {
                  showOverlayLoader();
            }).on('search.dt', function() {
                  showOverlayLoader();
            });
      });
</script>
@endsection