@extends('store.master.main')

@section('styles')
	<?php $styles = [
		'vendor/bootstrap/css/bootstrap.min.css',
		'fonts/font-awesome-4.7.0/css/font-awesome.min.css',
		'fonts/iconic/css/material-design-iconic-font.min.css',
		'fonts/linearicons-v1.0.0/icon-font.min.css',
		'vendor/animate/animate.css',
		'vendor/css-hamburgers/hamburgers.min.css',
		'vendor/animsition/css/animsition.min.css',
		'vendor/select2/select2.min.css',
		'vendor/perfect-scrollbar/perfect-scrollbar.css',
		'css/util.css',
		'css/main.css',
		'css/font.css',
	]; ?>

	@foreach ($styles as $style)
		<link rel="stylesheet" type="text/css" href="{{ asset($style) }}">
	@endforeach

	<style>
	table {
		text-align: center;
	}
	th {
		text-align: center !important;
	}
	</style>
@endsection
	
@section('article')
	<!-- breadcrumb -->
	<div class="container" dir="rtl">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
				صفحه اصلی
				<i class="fa fa-angle-left m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				سبد خرید
			</span>
		</div>
		
	</div>
	
	<!-- Shoping Cart -->
	<form class="bg0 p-t-75 p-b-85" dir="rtl">
		<div class="container">
			<div class="row">
				<div class="col-md-12" dir="rtl">
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
				</div>
				<div class="col-md-9 col-lg-10 col-xl-8 m-lr-auto m-b-50">
					<div class="m-r-10 m-lr-0-xl">
						<div class="wrap-table-shopping-cart">
							<table class="table-shopping-cart">
								@if(!empty($cart_products[0]))
								<tr class="table_head">
									<th class="column-1">تصویر</th>
									<th class="column-2">نام</th>
									<th class="column-3">قیمت</th>
									<th class="column-4">تعداد</th>
									<th class="column-5">مجموع</th>
								</tr>
								@endif
								<?php $total = 0; ?>
								@empty($cart_products[0])
									<div class="alert alert-warning alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										هیچ محصولی در سبد خرید موجود نیست !
									</div>
								@else
								
								<?php $i = 0; ?>
								@foreach ($cart_products as $product)
									<tr class="table_row">
										<td class="column-1">
											<div class="how-itemcart1" onclick="window.location = '/cart/remove/{{$product->pro_id}}/{{$product->name}}'">
												<img src="{{ asset('uploads/'.$product->photo) }}" alt="Product Image">
											</div>
										</td>
										<td class="column-2"><a href="/product/{{$product->pro_id}}"><b>{{$product->name}}</b></a></td>
										<?php
										$price = $product->price;
										if ($product->unit)
											$price = $price * $dollar_cost;

										if ($product -> offer != 0)
											$price = $price - ($product->offer * $price) / 100;  
										?>
										<td class="column-3"><span class="num-comma">{{$price}}</span> تومان</td>
										<td class="column-4">
											<div class="wrap-num-product flex-w m-l-auto m-r-0">
												<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
													<i class="fs-16 zmdi zmdi-minus"></i>
												</div>
												
												<?php
												$cart =  json_decode(Cookie::get('cart'), true);
												if ($cart)
												{
													foreach ($cart as $key => $item)
													{
														if ($item['id'] == $product->pro_id) { $count = $item['count']; }
													} 
												}
												?>
												<input class="mtext-104 cl3 txt-center num-product" type="text" name="num-product1" value="{{$count}}">
	
												<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
													<i class="fs-16 zmdi zmdi-plus"></i>
												</div>
											</div>
										</td>
										<?php $total += $price * $count; ?>
										<td class="column-5"><span class="price num-comma">{{$price * $count}}</span> تومان</td>
									</tr>
									@endforeach
								@endempty
							</table>
						</div>

						<div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
							<div class="flex-w flex-m m-l-20 m-tb-5">
								<input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-l-10 m-tb-5" type="text" name="coupon" placeholder="کد تخفیف">
									
								<div class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
									ثبت کد تخفیف
								</div>
							</div>

							<div class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
								افزودن به سبد خرید
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-3 col-sm-10 col-lg-7 col-xl-4 m-lr-auto m-b-50">
					<div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-10 m-lr-0-xl p-lr-15-sm">
						<h4 class="mtext-109 cl2 p-b-30">
							سبد خرید
						</h4>

						<div class="flex-w flex-t bor12 p-b-13">
							<div class="size-208">
								<span class="stext-110 cl2">
									قیمت محصولات:
								</span>
							</div>

							<div class="size-209">
								<span class="mtext-110 cl2">
									<span class="total num-comma">{{$total}}</span> تومان
								</span>
							</div>
						</div>

						<div class="flex-w flex-t bor12 p-t-15 p-b-30">
							<div class="size-208 w-full-ssm">
								<span class="stext-110 cl2">
									حمل و نقل:
								</span>
							</div>

							<div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
								<p class="stext-111 cl6 p-t-2">
									هیچ روش حمل و نقل در دسترس نیست. لطفا آدرس خود را بررسی کنید، یا اگر به کمک نیاز دارید، با ما تماس بگیرید.
								</p>
								
								<div class="p-t-15">
									<span class="stext-112 cl8">
										هزینه حمل و نقل
									</span>

									<div class="rs1-select2 rs2-select2 bor8 bg0 m-b-12 m-t-9">
										<select class="js-select2" name="time">
											<option>انتخاب کشور ....</option>
											<option>ایران</option>
											<option>انگلستان</option>
										</select>
										<div class="dropDownSelect2"></div>
									</div>

									<div class="bor8 bg0 m-b-12">
										<input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="state" placeholder="کشور / استان">
									</div>

									<div class="bor8 bg0 m-b-22">
										<input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="postcode" placeholder="کد پستی">
									</div>
									
									<div class="flex-w">
										<div class="flex-c-m stext-101 cl2 size-115 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
											بروز رسانی
										</div>
									</div>
										
								</div>
							</div>
						</div>

						<div class="flex-w flex-t p-t-27 p-b-33">
							<div class="size-208">
								<span class="mtext-101 cl2">
									قیمت کل:
								</span>
							</div>

							<div class="size-209 p-t-1">
								<span class="mtext-110 cl2">
									<span class="total num-comma">{{$total}}</span> تومان
								</span>
							</div>
						</div>

						<button class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
							تایید 
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>
@endsection

@section('scripts')
	<!--===============================================================================================-->	
		<script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
	<!--===============================================================================================-->
		<script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
	<!--===============================================================================================-->
		<script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
		<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
	<!--===============================================================================================-->
		<script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
		<script>
			$(".js-select2").each(function(){
				$(this).select2({
					minimumResultsForSearch: 20,
					dropdownParent: $(this).next('.dropDownSelect2')
				});
			})
		</script>
	<!--===============================================================================================-->
		<script src="{{ asset('vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
	<!--===============================================================================================-->
		<script src="{{ asset('vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
		<script>
			$('.js-pscroll').each(function(){
				$(this).css('position','relative');
				$(this).css('overflow','hidden');
				var ps = new PerfectScrollbar(this, {
					wheelSpeed: 1,
					scrollingThreshold: 1000,
					wheelPropagation: false,
				});

				$(window).on('resize', function(){
					ps.update();
				})
			});
		</script>
	<!--===============================================================================================-->
		<script src="{{ asset('js/numeral.min.js') }}"></script>
		<script>
			var nums = document.getElementsByClassName('num-comma');

			for (num in nums) {
				nums[num].innerHTML = numeral(nums[num].innerHTML).format('0,0');
			}
		</script>
		
		<script src="{{ asset('js/main.js') }}"></script>
@endsection