@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto"> </h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> </span>
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

					<div class="col-xl-12">
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-0">الزبائن  </h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-striped mg-b-0 text-md-nowrap">
										<thead>
											<tr>
												<th>ID</th>
                                                <th>Name</th>
												<th>National_id</th>
												<th>Phone</th>
                                                <th>Gedner</th>
                                                <th>mobile</th>
                                                <th>address</th>
                                                <th>address_details</th>
                                                <th>notes</th>
                                                <th>operations</th>

											</tr>
										</thead>
										<tbody>
                                        @php
                                        $count = 1;
                                         @endphp
											 @foreach ($customers as $customer)
                                                <tr>
                                                  <th scope="row">{{ $count++ }}</th>
                                                  <td>{{ $customer->name }}</td>
                                                  <td>{{ $customer->national_id }}</td>
                                                  <td>{{ $customer->phone_number }}</td>
                                                  <td>{{ $customer->gender }}</td>
                                                  <td>{{ $customer->mobile }}</td>
                                                  <td>{{ $customer->address }}</td>
                                                  <td>{{ $customer->address_detail }}</td>
                                                  <td>{{ $customer->notes }}</td>
                                                  <td>
                                    
                                            <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                            data-id="{{ $customer->id }}"  data-name="{{ $customer->name }}"
                                                data-phone_number="{{ $customer->phone_number }}"
                                                data-national_id="{{ $customer->national_id }}"  data-address="{{ $customer->address }}"
                                                data-gender="{{ $customer->gender }}"
                                                data-mobile="{{ $customer->mobile }}"
                                                data-address_detail="{{ $customer->address_detail }}" data-toggle="modal"
                                                href="#exampleModal2" title="Edit"><i class="las la-pen"></i></a>
                                       
                                            <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                data-id="{{ $customer->id }}" data-number="{{ $customer->number }}"
                                                data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                    class="las la-trash"></i></a>
                                      
                                                   </td>
                                                </tr>
                                             @endforeach

										</tbody>
									</table>
                                   
								</div><!-- bd -->
                                
							</div><!-- bd -->
                            <div class="col-xl-3">
                                    <a class="modal-effect btn btn-outline-primary btn-block"
                                     data-effect="effect-scale" data-toggle="modal" href="#modaldemo1">Add</a>
                                    </div>
						</div><!-- bd -->
					</div>
					<!--/div-->
     <!-- edit -->
     <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تعديل </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>   
                                                
                </div>
                <div class="modal-body">

                    <form action="{{ route('editcustomer') }}" method="post" autocomplete="off">
                       
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="recipient-name" class="col-form-label">name :</label>
                            <input class="form-control" name="name" id="name" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">national_id:</label>
                            <input class="form-control" id="national_id" name="national_id" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">phone_number:</label>
                            <input class="form-control" id="phone_number" name="phone_number" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">mobile:</label>
                            <input class="form-control" id="mobile" name="mobile" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">gender:</label>
                            <input class="form-control" id="gender" name="gender" type="string">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">address:</label>
                            <input class="form-control" id="address" name="address" type="string">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">address_detail:</label>
                            <input class="form-control" id="address_detail" name="address_detail" type="string">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تاكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- delete -->
    <div class="modal" id="modaldemo9">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">حذف </h6><button aria-label="Close" class="close" data-dismiss="modal"
                        type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('deletetruck') }}" method="post">
                  
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="number" id="number" type="integer" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
					<!-- Basic modal -->
		<div class="modal" id="modaldemo1">
			<div class="modal-dialog" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title"> Add </h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
                    <form action="{{ route('addcustomer') }}" method="post">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="exampleInputEmail1"> Name </label>
                            <input type="string" class="form-control" id="name" name="name">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">phone</label>
                            <input type="integer" class="form-control" id="phone" name="phone" >
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">national_id</label>
                            <input type="integer" class="form-control" id="national_id" name="national_id" >
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">mobile</label>
                            <input type="integer" class="form-control" id="mobile" name="mobile" >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> gender </label>
                            <input type="string" class="form-control" id="gender" name="gender">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> address </label>
                            <input type="string" class="form-control" id="address" name="address">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> address_detail </label>
                            <input type="string" class="form-control" id="address_detail" name="address_detail">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> Notes </label>
                            <input type="string" class="form-control" id="notes" name="notes">
                        </div>
					</div>
					<div class="modal-footer">
                    <button type="submit" class="btn btn-success">save</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">cancel</button>
					</div>
                    </form>
				</div>
 
                
			</div>
		</div>
		<!-- End Basic modal -->


    

				<!-- /row -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
@endsection
@section('js')
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<script src="{{ URL::asset('assets/js/modal.js') }}"></script>


<script>
    $('#exampleModal2').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var phone_number = button.data('phone_number')
        var national_id = button.data('national_id')
        var gender = button.data('gender')
        var mobile = button.data('mobile')
        var address = button.data('address')
        var address_detail = button.data('address_detail')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #phone_number').val(phone_number);
        modal.find('.modal-body #national_id').val(national_id);
        modal.find('.modal-body #gender').val(gender);
        modal.find('.modal-body #mobile').val(mobile);
        modal.find('.modal-body #address').val(address);
        modal.find('.modal-body #address_detail').val(address_detail);
    })

    
</script>

<script>
    $('#modaldemo9').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var number = button.data('number')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #number').val(number);
    })

</script>
@endsection
