@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الصفحة الشخصية :</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"></span>
						</div>
					</div> 
					
					</div>
				</div>
				<!-- breadcrumb -->
@endsection

@section('content')
@if (session()->has('edit'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session()->get('edit') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
				<!-- row -->
				<div class="row row-sm">
					<div class="col-lg-4">
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0"> 
									<div class="main-profile-overview">
										<div class="main-img-user profile-user">
											<img alt="" src="{{URL::asset('assets/img/brand/logo2.png')}}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
										</div>
										<div class="d-flex justify-content-between mg-b-20">
											<div>
												<h5 class="main-profile-name">@if (auth()->guard('admin_web')->check())
                                                    {{ auth()->guard('admin_web')->user()->name }}
                                                    @elseif (auth()->guard('emp_web')->check())
                                                   {{ auth()->guard('emp_web')->user()->name }}
											   @endif</h5>
												<p class="main-profile-name-text">@if (auth()->guard('admin_web')->check())
                                                    {{ auth()->guard('admin_web')->user()->email }}
                                                    @elseif (auth()->guard('emp_web')->check())
                                                   {{ auth()->guard('emp_web')->user()->email }}
											   @endif</p>
											</div>
										</div>
										<h6>الوصف</h6>
										<div class="main-profile-bio">
                                     <h2>مدير شركة العودة لشحن البضائع بين المحافظات السورية
									 </h2>
									</div><!-- main-profile-bio -->
										<div class="row">
											<div class="col-md-4 col mb20">
											
											</div>
										</div>
										<hr class="mg-y-30">
										<label class="main-content-label tx-13 mg-b-20"><h4>  رقم الهاتف :</h4>
										<h5>	@if (auth()->guard('admin_web')->check())
                                             {{ auth()->guard('admin_web')->user()->phone_number }}       
											   @endif</label></h5>
										<div class="main-profile-social-list">
											
										</div>
										<hr class="mg-y-30">
										<h6></h6>
										<div class="skill-bar mb-4 clearfix mt-3">
											
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											
										</div>
										<!--skill bar-->
										<div class="skill-bar clearfix">
											
										</div>
										<!--skill bar-->
									</div><!-- main-profile-overview -->
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="row row-sm">
							
						</div>
						<div class="card">
							<div class="card-body">
								<div class="tabs-menu ">
									<!-- Tabs -->
									<ul class="nav nav-tabs profile navtab-custom panel-tabs">
									
										<li class="">
											<a href="#settings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="las la-cog tx-16 mr-1"></i></span> <span class="hidden-xs">الإعدادات</span> </a>
										</li>
									</ul>
								</div>
								<div class="tab-content border-left border-bottom border-right border-top-0 p-4">
								
									<div class="tab-pane " id="settings">
									<form action="{{ route('editprofile') }}" method="post" autocomplete="off">
                       
					                 {{ csrf_field() }}
											<div class="form-group">
											<input type="hidden" name="id" id="id" value="{{auth()->guard('admin_web')->user()->id}}">
												<label for="FullName"  >الاسم </label>
												<input class="form-control"   value="{{auth()->guard('admin_web')->user()->name}}" name="name" id="name" type="string">

											</div>
											<div class="form-group">
												<label for="Email">الايميل</label>
												<input class="form-control"  value="{{auth()->guard('admin_web')->user()->email}}"  name="email" id="email" type="string">											</div>
											<div class="form-group">
												<label for="Username">رقم_الهاتف</label>
												<input class="form-control"  value="{{auth()->guard('admin_web')->user()->phone_number}}" name="phone_number" id="phone_number" type="integer">											</div>
											<div class="form-group">
												<label for="Password">كلمة_المرور</label>
												<input type="password" placeholder="6 - 15 محرف"   name="password" id="Password" class="form-control">
											</div>
											
											
											<button type="submit" class="btn btn-primary" >حفظ</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
@endsection
@section('js')
@endsection