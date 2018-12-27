@extends('panel.master.main')

@section('styles')
	<?php $styles = [
		// Data table CSS
		'vendors/bower_components/datatables/media/css/jquery.dataTables.min.css',
		// select2 CSS
		'vendors/bower_components/select2/dist/css/select2.min.css',
		//alerts CSS
		'vendors/bower_components/sweetalert/dist/sweetalert.css',
		// Custom CSS
		'dist/css/style.css',
	]; ?>

	@foreach ($styles as $style)
	<link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
	@endforeach
@endsection
	
@section('content')
	<div class="container">

		<!-- Title -->
		<div class="row heading-bg">
			<!-- Breadcrumb -->
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<h5 class="txt-dark">
					@isset($spec) ویرایش جدول مشخصات فنی @else ثبت جدول مشخصات فنی جدید @endisset
				</h5>
			</div>
		
			<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
				<ol class="breadcrumb">
					<li class="active">
						<span>@isset($spec) ویرایش جدول مشخصات فنی @else ثبت جدول مشخصات فنی جدید @endisset</span>
					</li>
					<li>فروشگاه</li>
					<li>داشبورد</li>
				</ol>
			</div>
			<!-- /Breadcrumb -->
		</div>
		<!-- /Title -->

		<!-- Row -->
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default card-view">
					<div class="panel-wrapper collapse in">
						<div class="panel-body pt-0">
							<div class="form-wrap">
								<form action="@isset($spec) {{ route('specification.update', ['specification' => $spec->id]) }} @else {{ route('specification.store') }} @endisset" method="POST">
									<h6 class="txt-dark flex flex-middle  capitalize-font"><i class="font-20 txt-grey zmdi zmdi-info-outline ml-10"></i>مشخصات برند</h6>
									<hr class="light-grey-hr"/>
									
									<div class="panel-body">
										@foreach ($errors -> all() as $message)
											<div class="alert alert-danger alert-dismissable">
												<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
												{{ $message }} 
											</div>
										@endforeach

										@if(session()->has('message'))
											<div class="alert alert-success alert-dismissable">
												<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
												{{ session()->get('message') }}
											</div>
										@endif
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label mb-10">گروه</label>
												<div class="input-group">
													<select name="parent" class="form-control select2 categories">
														@if (isset($category))
														<option value="{{$category->id}}">برای گروه {{ $category->title }}</option>
														@else
														<option value="">یک گروه انتخاب کنید</option>
														@endif

														@foreach ($categories as $item)
														<option value="{{ $item->id}}">{{$item->title}}</option>
														@endforeach
													</select>
													<div class="input-group-addon"><i class="ti-layout-grid2-alt"></i></div>
												</div>
											</div>
										</div>
									</div>

									<hr class="light-grey-hr"/>
									
									<div class="form-actions">
										<button class="btn @isset($spec) btn-warning @else btn-primary @endisset btn-icon right-icon mr-10 pull-left"> <i class="fa fa-check"></i>
											<span>@isset($spec) ویرایش @else ثبت @endisset جدول مشخصات فنی</span>
										</button>
										<div class="clearfix"></div>
									</div>

									@csrf

									@isset($spec) @method('put') @endisset
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Row -->

		<!-- Title -->
		<div class="row heading-bg">
			<!-- Breadcrumb -->
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
				<h5 class="txt-dark">لیست جداول مشخصات فنی</h5>
			</div>

			<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"></div>
			<!-- /Breadcrumb -->
		</div>
		<!-- /Title -->
		<!-- Row -->	
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default border-panel card-view">
					<div class="panel-wrapper collapse in">
						<div class="panel-body">
							<div class="table-wrap">
								<div class="table-responsive">
									<table id="datable_2" class="table table-hover table-bordered display mb-30" >
										<thead>
											<tr>
												<th>#</th>
												<th>گروه</th>
												<th>تاریخ ثبت</th>
												<th>تاریخ آخرین ویرایش</th>
												<th>عملیات</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>#</th>
												<th>گروه</th>
												<th>تاریخ ثبت</th>
												<th>تاریخ آخرین ویرایش</th>
												<th>عملیات</th>
											</tr>
										</tfoot>
										<tbody>
											@php $i = 0 @endphp

											@foreach ($specs as $item)
											<tr>
												<td>{{ ++$i }}</td>
												<td>{{ $item->category->title }}</td>
												<td>{{ \Morilog\Jalali\Jalalian::forge($item->created_at)->format('%H:%S - %d %B %Y') }}</td>
												<td>{{ \Morilog\Jalali\Jalalian::forge($item->updated_at)->ago() }}</td>
												<td>
													<form action="{{ route('specification.destroy', ['specification' => $item->id]) }}" method="POST">
														<a href="{{ route('header.index', ['specification' => $item->id]) }}" class="btn btn-warning"><i class="icon ti-pencil"></i> ویرایش</a>
														<a href="{{ route('specification.edit', ['specification' => $item->id]) }}" class="btn btn-info"><i class="icon ti-layout-grid2-alt"></i> تغییر گروه</a>
														<button type="submit" itemid="{{ $item->id }}" class="btn btn-danger delete-item"><i class="icon ti-close"></i> حذف</button>
														
														@method('delete')
														@csrf
													</form>	
												</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>	
						</div>	
					</div>	
				</div>	
			</div>
		</div>
		<!-- /Row -->
	</div>
@endsection
		
		
@section('scripts')
	<?php $scripts = [
		// jQuery
		'vendors/bower_components/jquery/dist/jquery.min.js',
		// Bootstrap Core JavaScript
		'vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
		// Slimscroll JavaScript
		'dist/js/jquery.slimscroll.js',
		// Tinymce JavaScript
		'vendors/bower_components/tinymce/tinymce.min.js',
		// Tinymce Wysuhtml5 Init JavaScript
		'dist/js/tinymce-data.js',
		// Bootstrap Daterangepicker JavaScript
		'vendors/bower_components/dropify/dist/js/dropify.min.js',
		// Data table JavaScript
		'vendors/bower_components/datatables/media/js/jquery.dataTables.min.js',
		'dist/js/dataTables-data.js',
		// Fancy Dropdown JS
		'dist/js/dropdown-bootstrap-extended.js',
		// Select2 JavaScript
		'vendors/bower_components/select2/dist/js/select2.full.min.js',
		// Owl JavaScript
		'vendors/bower_components/owl.carousel/dist/owl.carousel.min.js',
		// Bootstrap Tagsinput JavaScript
		'vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js',
		// Sweet-Alert 
		'vendors/bower_components/sweetalert/dist/sweetalert.min.js',
		// Init JavaScript
		'dist/js/init.js',
		// Init Add Product Page JavaScript
		'dist/js/group_ajax.js',
	]; ?>

	@foreach ($scripts as $script)
	<script src="{{ asset($script) }}"></script>
	@endforeach
	
	<script>
		$('.delete-item').on('click',function(event){
			event.preventDefault();
			var title = $(this).parent().parent().prev().prev().prev().text();
			var id = $(this).attr('itemid');
			var form = $(this).parent();

			swal({   
				title: "مطمین هستید ؟",   
				text: "برای پاک کردن برند " + title + " مطمین هستید ؟",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#f83f37",   
				confirmButtonText: "بله",   
				cancelButtonText: "خیر",   
				closeOnConfirm: false,   
				closeOnCancel: false 
			}, function(isConfirm){   
				if (isConfirm) {
					form.submit();
				} else {     
					swal("لغو شد", "هیچ برندی حذف نشد :)", "error");   
				} 
			});
			return false;
		});
	</script>
@endsection