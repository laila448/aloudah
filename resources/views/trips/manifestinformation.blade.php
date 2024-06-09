@extends('layouts.master')
@section('css')
<!-- Internal Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">Manifest: </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"></span>
						</div>
					</div>
					<div class="d-flex my-xl-auto right-content">
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-filter-variant"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-danger btn-icon ml-2"><i class="mdi mdi-star"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-refresh"></i></button>
						</div>
						<div class="mb-3 mb-xl-0">
							<div class="btn-group dropdown">
								<button type="button" class="btn btn-primary">14 Aug 2019</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuDate" data-x-placement="bottom-end">
									<a class="dropdown-item" href="#">2015</a>
									<a class="dropdown-item" href="#">2016</a>
									<a class="dropdown-item" href="#">2017</a>
									<a class="dropdown-item" href="#">2018</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
@if (session()->has('Add'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('Add') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
  
@if (session()->has('delete'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('delete') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session()->has('edit'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('edit') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-0"> </h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 tx-gray-500 mb-2"> 
								<div class="container">
                                  <div class="row">
                                   <div class="col-sm">
								   Source: {{ $trip->branch->address }}
								   <br></br>
                                   Destination: {{ $trip->destination->address }}
                                </div>
                                    <div class="col-sm">
                                	Trip Number: {{ $trip->number }}
                                </div>
                                   <div class="col-sm">
	                             Driver: {{ $trip->driver->name }}
								 <br></br>
                                 Truck: {{ $trip->truck->number }}
                                      </div>
                                   </div>
                               </div>		</p>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap" id="example1">
										<thead>
											<tr>
                                            <th class="wd-15p border-bottom-0">ID </th>
											<th class="wd-15p border-bottom-0">Source </th>
												<th class="wd-15p border-bottom-0">Sender </th>
                                                <th class="wd-15p border-bottom-0">Reciver </th>
                                                <th class="wd-15p border-bottom-0">Sender-number </th>
                                                <th class="wd-15p border-bottom-0">Reciver-number </th>
                                                <th class="wd-15p border-bottom-0"> content</th>
                                                <th class="wd-15p border-bottom-0"> Weight</th>
                                                <th class="wd-15p border-bottom-0"> Shipping_cost</th>
                                                <th class="wd-15p border-bottom-0"> against_shipping</th>
                                                <th class="wd-15p border-bottom-0"> adapter</th>
                                                <th class="wd-15p border-bottom-0"> discount</th>
                                                <th class="wd-15p border-bottom-0"> collection</th>
											</tr>
										</thead>   
										<tbody>
											
											 @foreach ($shippings as $shipping)
                                                <tr>  
												<td>{{"#"}}{{ $shipping->number }}</td>   
                                                <td>{{ $shipping->branchSource->address }}</td>
                                                  <td>{{ $shipping->sender }}</td>
                                                  <td>{{ $shipping->receiver }}</td>
                                                  <td>{{ $shipping->sender_number }}</td>
                                                  <td>{{ $shipping->receiver_number }}</td>
                                                  <td>{{ $shipping->content }}</td>
                                                  <td>{{ $shipping->weight }}</td>
                                                  <td>{{ $shipping->shipping_cost }}</td>
                                                  <td>{{ $shipping->against_shipping }}</td>
                                                  <td>{{ $shipping->adapter }}</td>
                                                  <td>{{ $shipping->discount }}</td>
                                                  <td>{{ $shipping->collection }}</td>
                                                 
  

											</tr>
											@endforeach

										</tbody>
									</table>
								</div>
								<div class="container">
								<br></br>
									<br></br>
                                  <div class="row">
								 
                                   <div class="col-sm">
									
								   Status: {{ $manifest->status }}
								   <br></br>
                                   General Total: {{ $manifest->general_total }}
                                </div>
                                    <div class="col-sm">
                                	
                                </div>
                                   <div class="col-sm">
								   Discount: {{ $manifest->discount }}
								 <br></br>
                                 Net Total: {{ $manifest->net_total }}
								
                                      </div>
									  <br></br>
								 <br></br>
								 <br></br>
                                   </div>
                               </div>	
							</div>
							
							
						</div>
					</div>
					<!--/div-->

					<!--div-->
				
					</div>
					<!--/div-->
 
    </div>

					
				<!-- /row -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
@endsection
@section('js')
<!-- Internal Data tables -->
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
<!--Internal  Datatable js -->
<script src="{{URL::asset('assets/js/table-data.js')}}"></script>


@endsection