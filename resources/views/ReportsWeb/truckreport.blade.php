@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">  تقارير :</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> </span>
						</div>
					</div>
					
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')

				<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
           <div class="center">

           <h4>     رحلات الشاحنة   {{$truck->number}} : </h4>
           

        </div>

                <div class="d-flex justify-content-between">
                    <h1 class="card-title mg-b-0">  </h1>
                    <i class="mdi mdi-dots-horizontal text-gray"></i>
                </div>
            </div>

            <div class="container">


    @if($reportData->isEmpty())
        <p>لا توجد رحلات لهذا السائق في الفترة المحددة.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الرحلة</th>
                    <th>السائق </th>
                    <th>الفرع</th>
                    <th>الوجهة</th>
                    <th>تاريخ الرحلة</th>
                </tr>
            </thead>
            <tbody>
            @php
                     $count = 1;
                           @endphp
                @foreach($reportData as $data)
                    <tr>
                        <th scope="row">{{ $count++ }}</th>
                        <td>{{ $data['trip_number'] }}</td>
                        <td>{{ $data['driver'] }}</td>
                        <td>{{ $data['branch_desk'] }}</td>
                        <td>{{ $data['destination'] }}</td>
                        <td>{{ $data['trip_date'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
  
    
            </div>
        </div><!-- bd -->
    </div>
</div>
<!-- row closed -->


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


@endsection
