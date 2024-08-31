@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">مدراء الأفرع    :</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> </span>
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
									<h4 class="card-title mg-b-0">المدراء: </h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-striped mg-b-0 text-md-nowrap">
										<thead>
											<tr>
												<th>ID</th>
                                                <th>الاسم</th>
                                                <th>الرقم الوطني</th>
												<th>رقم_الهاتف</th>
                                                <th>العنوان</th>
                                                <th>الفرع</th>
                                                <th>المكتب</th>
                                                <th>العمليات</th>
											</tr>
										</thead>
										<tbody>
                                        @php
                                        $count = 1;
                                         @endphp
											 @foreach ($managers as $manager)
                                                <tr>
                                                  <th scope="row">{{ $count++ }}</th>
                                                  <td>{{ $manager->name }}</td>
                                                  <td>{{ $manager->national_id }}</td>                                                  
                                                  <td>{{ $manager->phone_number }}</td>
                                                  <td>{{ $manager->manager_address }}</td>

                                                  <td>{{ $manager->branch->address }}</td>
                                                  <td>{{ $manager->branch->desk }}</td>
                                           
                                            <!-- manager -->

                                                  <td>
                                                  <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                data-id="{{ $manager->id }}" data-name="{{ $manager->name }}"
                                                data-phone_number="{{ $manager->phone_number }}"  
                                                data-national_id="{{ $manager->national_id }}" 
                                                data-manager_address="{{ $manager->manager_address }}" data-toggle="modal"
                                                href="#exampleModal2" title="تعديل"><i class="las la-pen"></i></a>
                                                
                                              
                                           
                                            <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                data-id="{{ $manager->id }}" data-name="{{ $manager->name }}"
                                                data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                    class="las la-trash"></i></a>
                                      
                                                   </td>
                                                </tr>
                                             @endforeach

										</tbody>
									</table>
                                   
								</div><!-- bd -->
                                
							</div><!-- bd -->
                          
                            <!--manager  -->
                            <div class="col-xl-3">
                                    <a class="modal-effect btn btn-outline-primary btn-block"
                                     data-effect="effect-scale" data-toggle="modal" href="#modaldemo1">إضافة   </a>
                                    </div>

                                    
						</div><!-- bd -->
                        
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

                    <form action="{{ route('editbranchmanager') }}" method="post" autocomplete="off">
                       
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="hidden" name="id" id="id" value="">
                            <label for="recipient-name" class="col-form-label">الاسم :</label>
                            <input class="form-control" name="name" id="name" type="string">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">رقم_الهاتف:</label>
                            <input class="form-control" id="phone_number" name="phone_number" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">الرقم_الوطني:</label>
                            <input class="form-control" id="national_id" name="national_id" type="integer">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">العنوان:</label>
                            <input class="form-control" id="manager_address" name="manager_address" type="string">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
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
                <form action="{{ route('deletebranchmanager') }}" method="post">
                  
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="name" id="name" type="string" readonly>
                    </div>
                    <div class="modal-footer">
                       <button type="submit" class="btn btn-danger">حذف</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        
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
						<h6 class="modal-title">  إضافة مدير فرع:</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
                    <form action="{{ route('addbranchmanager') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1"> الاسم</label>
                            <input type="string" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> الايميل</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"> كلمة_المرور</label>
                            <input type="string" class="form-control" id="password" name="password">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1"> العنوان</label>
                            <input type="string" class="form-control" id="manager_address" name="manager_address">
                        </div>

                      
						<div class="form-group">
                            <label for="exampleFormControlTextarea1">رقم_الهاتف</label>
                            <input type="integer" class="form-control" id="phone_number" name="phone_number" >
                        </div>
                        <div class="form-group">
                             <label for="exampleFormControlSelect1">الفرع</label>
                        <select class="form-control" id="branch_id" name="branch_id">
                         <option value="">اختر الفرع</option>
                           @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->desk }}</option>
                                 @endforeach
                                 </select>
                          </div>
                          <div class="form-group">
                            <label for="exampleFormControlTextarea1">الرقم_الوطني</label>
                            <input type="integer" class="form-control" id="national_id" name="national_id" >
                        </div>
                        <div class="form-group">
                      <label for="gender">الجنس</label>
                         <select class="form-control" id="gender" name="gender">
                          <option value="">اختر الجنس</option>
                         <option value="male">ذكر</option>
                         <option value="female">أنثى</option>
                       </select>
                    </div>
                        	<div class="form-group">
                            <label for="exampleFormControlTextarea1">اسم_الام</label>
                            <input type="string" class="form-control" id="mother_name" name="mother_name" >
                        </div>	
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">تاريخ_الميلاد</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" >
                        </div>	
                      
					<div class="modal-footer">
                    <button type="submit" class="btn btn-success">إضافة</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">الغاء</button>
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
        var manager_address = button.data('manager_address')

        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #phone_number').val(phone_number);
        modal.find('.modal-body #national_id').val(national_id);
        modal.find('.modal-body #manager_address').val(manager_address);

    })

</script>

<script>
    $('#modaldemo9').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
    })

</script>
@endsection
