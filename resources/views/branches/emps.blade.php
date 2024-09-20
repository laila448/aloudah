@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الموظفين  :</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> </span>
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
 
                                    <!-- row opened -->
                                    <div class="row row-sm">
   
    </div>
</div>
								</div>
                               
						<div class="card">
							<div class="card-header pb-0">
                                
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-0"> </h4>
                                    
									<i class="mdi mdi-dots-horizontal text-gray"></i>

                                   

							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-striped mg-b-0 text-md-nowrap">
										<thead>
											<tr>
												<th>ID</th>
                                                <th>الاسم</th>												
												<th>رقم_الهاتف</th>
                                                <th>تاريخ_التوظيف</th>
                                                <th>ترقية_الموظف</th>

											</tr>
										</thead>
										<tbody>
                                        @php
                                        $count = 1;
                                         @endphp
											 @foreach ($emps as $employee)
                                                <tr>
                                                  <th scope="row">{{ $count++ }}</th>
                                                  <td>{{ $employee->name }}</td>
                                                  <td>{{ $employee->phone_number }}</td>
                                                  <td>{{ $employee->employment_date }}</td>
                                                   <td>  
                                                    
                                                   <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                data-id="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                                data-national_id="{{ $employee->national_id }}"
                                                data-email="{{ $employee->email }}"
                                                data-password="{{ $employee->password }}"
                                                data-phone_number="{{ $employee->phone_number }}"
                                                data-branch_id="{{ $employee->branch_id }}"
                                                data-mother_name="{{ $employee->mother_name }}"
                                                data-gender="{{ $employee->gender }}"
                                                data-address="{{ $employee->address }}"
                                                data-birth_date="{{ $employee->birth_date }}"
                                                  data-toggle="modal" 
                                                href="#exampleModal2" title="ترقية لمدير فرع"><i class="las la-pen"></i></a>


                                            <a class="modal-effect btn btn-sm btn-success" data-effect="effect-scale"
                                                data-id="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                                data-national_id="{{ $employee->national_id }}"
                                                data-email="{{ $employee->email }}"
                                                data-password="{{ $employee->password }}"
                                                data-phone_number="{{ $employee->phone_number }}"
                                                data-branch_id="{{ $employee->branch_id }}"
                                                data-mother_name="{{ $employee->mother_name }}"
                                                data-gender="{{ $employee->gender }}"
                                                data-address="{{ $employee->address }}"
                                                data-birth_date="{{ $employee->birth_date }}"
                                                data-toggle="modal" href="#lili" title="ترقية لمدير مستودع"><i
                                                    class="las la-pen"></i></a></td>



                                                  
                               
                                                </tr>
                                             @endforeach

										</tbody>
									</table>
                                   
								</div><!-- bd -->
                                
							</div><!-- bd -->
						<!-- edit -->
  <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ترقية الموظف لمدير فرع </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <form action="{{ route('PromoteEmp') }}" method="post" autocomplete="off">
                       
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="recipient-name" class="col-form-label" >الاسم :</label>
                            <input class="form-control" name="name" id="name" type="string"  readonly >
                            
                            <input  type="hidden" name="national_id" id="national_id" type="integer"  readonly >
                            <input   type="hidden" name="email" id="email" type="string"  readonly >
                            <input  type="hidden" name="password" id="password" type="string"  readonly >
                            <input  type="hidden" name="phone_number" id="phone_number" type="integer"  readonly >
                            <input  type="hidden" name="branch_id" id="branch_id" type="integer"  readonly >
                            <input  type="hidden" name="mother_name" id="mother_name" type="string"  readonly >
                            <input  type="hidden" name="gender" id="gender" type="string"  readonly >
                            <input  type="hidden" name="address" id="address" type="string"  readonly >
                            <input  type="hidden" name="birth_date" id="birth_date" type="string"  readonly >

                        
                        </div>
                       
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">تاكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ننننن -->
    <div class="modal fade" id="lili" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ترقية الموظف لمدير مستودع </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <form action="{{ route('PromoteEmpwh') }}" method="post" autocomplete="off">
                       
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="recipient-name" class="col-form-label" >الاسم :</label>
                            <input class="form-control" name="name" id="name" type="string"  readonly >
                            
                            <input  type="hidden" name="national_id" id="national_id" type="integer"  readonly >
                            <input   type="hidden" name="email" id="email" type="string"  readonly >
                            <input  type="hidden" name="password" id="password" type="string"  readonly >
                            <input  type="hidden" name="phone_number" id="phone_number" type="integer"  readonly >
                            <input  type="hidden" name="branch_id" id="branch_id" type="integer"  readonly >
                            <input  type="hidden" name="mother_name" id="mother_name" type="string"  readonly >
                            <input  type="hidden" name="gender" id="gender" type="string"  readonly >
                            <input  type="hidden" name="address" id="address" type="string"  readonly >
                            <input  type="hidden" name="birth_date" id="birth_date" type="string"  readonly >

                        
                        </div>
                       
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">تاكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ظسس -->
<!-- delete -->

<div class="modal" id="modaldemo9">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">حذف </h6><button aria-label="Close" class="close" data-dismiss="modal"
                        type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('PromoteEmpwh') }}" method="post" autocomplete="off">
                       
                       {{ csrf_field() }}
                       <div class="form-group">
                           <input type="hidden" name="id" id="id" value="">
                           <label for="recipient-name" class="col-form-label" >الاسم :</label>
                           <input class="form-control" name="name" id="name" type="string"  readonly >
                           
                           <input  type="hidden" name="national_id" id="national_id" type="integer"  readonly >
                           <input   type="hidden" name="email" id="email" type="string"  readonly >
                           <input  type="hidden" name="password" id="password" type="string"  readonly >
                           <input  type="hidden" name="phone_number" id="phone_number" type="integer"  readonly >
                           <input  type="hidden" name="branch_id" id="branch_id" type="integer"  readonly >
                           <input  type="hidden" name="mother_name" id="mother_name" type="string"  readonly >
                           <input  type="hidden" name="gender" id="gender" type="string"  readonly >
                           <input  type="hidden" name="address" id="address" type="string"  readonly >
                           <input  type="hidden" name="birth_date" id="birth_date" type="string"  readonly >

                       
                       </div>
                      
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-success">ترقية</button>
                    </div>
                </div>
            </form>



        </div>
    </div>
    </div>
					</div>
					<!--/div -->
 
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
        var password = button.data('password')
        var email = button.data('email')
        var national_id = button.data('national_id')
        var phone_number = button.data('phone_number')
        var branch_id = button.data('branch_id')
        var gender = button.data('gender')
        var address = button.data('address')
        var birth_date = button.data('birth_date')
       
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #password').val(password);
        modal.find('.modal-body #national_id').val(national_id);
        modal.find('.modal-body #email').val(email);
        modal.find('.modal-body #phone_number').val(phone_number);
        modal.find('.modal-body #branch_id').val(branch_id);
        modal.find('.modal-body #mother_name').val(mother_name);
        modal.find('.modal-body #gender').val(gender);
        modal.find('.modal-body #address').val(address);
        modal.find('.modal-body #birth_date').val(birth_date);


     
    })
   

</script>
<script>
    $('#lili').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var password = button.data('password')
        var email = button.data('email')
        var national_id = button.data('national_id')
        var phone_number = button.data('phone_number')
        var branch_id = button.data('branch_id')
        var gender = button.data('gender')
        var address = button.data('address')
        var birth_date = button.data('birth_date')
       
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #password').val(password);
        modal.find('.modal-body #national_id').val(national_id);
        modal.find('.modal-body #email').val(email);
        modal.find('.modal-body #phone_number').val(phone_number);
        modal.find('.modal-body #branch_id').val(branch_id);
        modal.find('.modal-body #mother_name').val(mother_name);
        modal.find('.modal-body #gender').val(gender);
        modal.find('.modal-body #address').val(address);
        modal.find('.modal-body #birth_date').val(birth_date);

    })

</script>
@endsection
