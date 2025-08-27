@extends('layouts.app')
@section('title', 'Report 607 (' . __('business.sale') . ')')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
      <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Sale Commission Report
      </h1>
</section>

<!-- Main content -->
<section class="content no-print">
      <div class="row">
            <div class="col-md-12">
                  @component('components.filters', ['title' => __('report.filters')])
                  {!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'sell_payment_report_form' ]) !!}
                  <div class="col-md-3">
                        <div class="form-group">
                              {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                              <div class="input-group">
                                    <span class="input-group-addon">
                                          <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'required','style' => 'width: 100%;']); !!}
                              </div>
                        </div>
                  </div>
                  <div class="col-md-3">
                        <div class="form-group">
                              {!! Form::label('location_id', __('purchase.business_location').':') !!}
                              <div class="input-group">
                                    <span class="input-group-addon">
                                          <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'required','style' => 'width: 100%;']); !!}
                              </div>
                        </div>
                  </div>
                  <div class="col-md-3">
                        <div class="form-group">
                              {!! Form::label('payment_types', __('lang_v1.payment_method').':') !!}
                              <div class="input-group">
                                    <span class="input-group-addon">
                                          <i class="fas fa-money-bill-alt"></i>
                                    </span>
                                    {!! Form::select('payment_types', $payment_types, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'required','style' => 'width: 100%;']); !!}
                              </div>
                        </div>
                  </div>
                  <div class="col-md-3">
                        <div class="form-group">
                              {!! Form::label('customer_group_filter', __('lang_v1.customer_group').':') !!}
                              <div class="input-group">
                                    <span class="input-group-addon">
                                          <i class="fa fa-users"></i>
                                    </span>
                                    {!! Form::select('customer_group_filter', $customer_groups, null, ['class' => 'form-control select2','style' => 'width: 100%;']); !!}
                              </div>
                        </div>
                  </div>
                  <div class="col-md-3">
                        <div class="form-group">

                              {!! Form::label('spr_date_filter', __('report.date_range') . ':') !!}
                              {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'spr_date_filter', 'readonly']); !!}
                        </div>
                  </div>
                  {!! Form::close() !!}
                  @endcomponent
            </div>
      </div>
      <div class="row">
            <div class="col-md-12">
                  @component('components.widget', ['class' => 'box-primary'])
                  <div class="table-responsive">
                        <table class="table table-bordered table-striped ajax_view" id="sale_commission_report_table">
                              <thead>
                                    <tr>
                                          <th>@lang('lang_v1.contact_id')</th>
                                          <th>@lang('sale.customer_name')</th>
                                          <th>@lang('sale.invoice_no')</th>
                                          <th>@lang('messages.date')</th>
                                          <th>@lang('sale.total') (@lang('product.exc_of_tax'))</th>
                                          <th>@lang('sale.discount')</th>
                                          <th>@lang('sale.tax')</th>
                                          <th>@lang('sale.total') (@lang('product.inc_of_tax'))</th>
                                          <th>@lang('lang_v1.payment_method')</th>
                                    </tr>
                              </thead>
                        </table>
                  </div>
                  @endcomponent
            </div>
      </div>
</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
      $(document).ready(function() {
            $('#sell_list_filter_date_range').daterangepicker(
                  dateRangeSettings,
                  function(start, end) {
                        $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                        sale_commission_report_table.ajax.reload();
                  }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                  $('#sell_list_filter_date_range').val('');
                  sale_commission_report_table.ajax.reload();
            });

            sale_commission_report_table = $('#sale_commission_report_table').DataTable({
                  processing: true,
                  serverSide: true,
                  aaSorting: [
                        [1, 'desc']
                  ],
                  "ajax": {
                        "url": "/sells",
                        "data": function(d) {
                              if ($('#sell_list_filter_date_range').val()) {
                                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                    d.start_date = start;
                                    d.end_date = end;
                              }
                              d.location_id = $('#sell_list_filter_location_id').val();
                              d.customer_id = $('#sell_list_filter_customer_id').val();
                              d.payment_status = $('#sell_list_filter_payment_status').val();
                              d = __datatable_ajax_callback(d);
                        }
                  },
                  columns: [{
                              data: 'contact_id',
                              name: 'contacts.contact_id'
                        },
                        {
                              data: 'name',
                              name: 'contacts.name'
                        },
                        {
                              data: 'invoice_no_text',
                              name: 'transactions.invoice_no'
                        },
                        {
                              data: 'sale_date',
                              name: 'transactions.transaction_date'
                        },
                        {
                              data: 'total_before_tax',
                              name: 'total_before_tax'
                        },
                        {
                              data: 'discount_amount',
                              name: 'discount_amount'
                        },
                        {
                              data: 'tax_amount',
                              name: 'tax_amount'
                        },
                        {
                              data: 'final_total',
                              name: 'final_total'
                        },
                        {
                              data: 'payment_methods',
                              name: 'payment_methods'
                        },
                  ],
                  "fnDrawCallback": function(oSettings) {
                        __currency_convert_recursively($('#sale_commission_report_table'));
                  }
            });

            $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs', function() {
                  sale_commission_report_table.ajax.reload();
            });
      });
</script>

@endsection