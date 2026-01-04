<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Reciept</title>
	@include('layouts/usersidebar')

	<style>
		#footer {
			position: absolute;
			bottom: 0;
			width: 100%;
			height: 60px;
			/* Height of the footer */
		}
	</style>
	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background: #fafafa;
		}

		table.heading {
			border-collapse: collapse;
			width: 100%;
		}

		table.heading th,
		table.heading td {
			border: 1px solid white;
			text-align: left;
			padding: 8px;
		}

		table.heading th {
			background-color: white;
			color: white;
		}
	</style>
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
		}

		th,
		td {
			border: 1px solid black;
			text-align: left;
			padding: 8px;
		}

		tr:nth-child(even) {
			background-color: #f2f2f2
		}

		th {
			background-color: #187f6a;
			color: white;
		}
	</style>
	<style>
		body {
			color: #2e323c;
			background: #f5f6fa;
			position: relative;
			height: 100%;
		}

		.invoice-container {
			padding: 5rem;
		}

		.invoice-container .invoice-header .invoice-logo {
			margin: 0.8rem 0 0 0;
			display: inline-block;
			font-size: 1.6rem;
			font-weight: 700;
			color: #2e323c;
		}

		.invoice-container .invoice-header .invoice-logo img {
			max-width: 130px;
		}

		.invoice-container .invoice-header address {
			font-size: 1.3 rem;
			color: #9fa8b9;
			margin: 0;
		}

		.invoice-container .invoice-details {
			margin: 1rem 0 0 0;
			padding: 1rem;
			line-height: 180%;
			background: #f5f6fa;
		}

		.invoice-container .invoice-details .invoice-num {
			text-align: right;
			font-size: 1.2rem;
		}

		.invoice-container .invoice-body {
			padding: 1rem 0 0 0;
		}

		.invoice-container .invoice-footer {
			text-align: center;
			font-size: 1.3rem;
			margin: 5px 0 0 0;
		}

		.invoice-status {
			text-align: center;
			padding: 1rem;
			background: #ffffff;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			margin-bottom: 1rem;
		}

		.invoice-status h2.status {
			margin: 0 0 0.8rem 0;
		}

		.invoice-status h5.status-title {
			margin: 0 0 0.8rem 0;
			color: #9fa8b9;
		}

		.invoice-status p.status-type {
			margin: 0.5rem 0 0 0;
			padding: 0;
			line-height: 150%;
		}

		.invoice-status i {
			font-size: 1.5rem;
			margin: 0 0 1rem 0;
			display: inline-block;
			padding: 1rem;
			background: #f5f6fa;
			-webkit-border-radius: 50px;
			-moz-border-radius: 50px;
			border-radius: 50px;
		}

		.invoice-status .badge {
			text-transform: uppercase;
		}

		@media (max-width: 767px) {
			.invoice-container {
				padding: 1rem;
			}
		}

		.custom-table {
			border: 1px solid #e0e3ec;
		}

		.custom-table thead {
			background: #007ae1;
		}

		.custom-table thead th {
			border: 0;
			color: #ffffff;
		}

		.custom-table>tbody tr:hover {
			background: #fafafa;
		}

		.custom-table>tbody tr:nth-of-type(even) {
			background-color: #ffffff;
		}

		.custom-table>tbody td {
			border: 1px solid #e6e9f0;
		}

		.card {
			background: #ffffff;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border: 0;
			margin-bottom: 1rem;
		}

		.text-success {
			color: #187f6a;
			!important;
		}

		.text-muted {
			color: #9fa8b9 !important;
		}

		.custom-actions-btns {
			margin: auto;
			display: flex;
			justify-content: flex-end;
		}

		.custom-actions-btns .btn {
			margin: .3rem 0 .3rem .3rem;
		}
	</style>
	<style>
		@media print {
			body {
				visibility: hidden;
			}

			.print-container,
			.print-container * {
				visibility: visible;
			}
		}

		@page {
			size: A4;
			margin: 0;
		}
	</style>

</head>

@php
    use App\Models\Softwareuser;
    use Illuminate\Support\Facades\DB;

    $userid = Session('softwareuser');
    $adminid = Softwareuser::Where('id', $userid)
        ->pluck('admin_id')
        ->first();
    $adminroles = DB::table('adminusers')
    ->leftJoin('module_roles', 'adminusers.id', '=', 'module_roles.user_id')
    ->where('user_id', $adminid)
    ->get();
@endphp

<body>
	<!-- Page Content Holder -->
	<div id="content">
        @if ($adminroles->contains('module_id', '30'))
        @include('navbar.expnavbar')
        @else
        <nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" id="sidebarCollapse" class="btn navbar-btn">
						<i class="glyphicon glyphicon-chevron-left"></i>
						<span></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="/generateinvoice">Back</a></li>
					</ul>
				</div>
			</div>
		</nav>
                @endif

		<div align="right">
			<a href="/printinvoice/{{$invoice_num}}" class="btn btn-primary">PRINT</a>
		</div>
		<br>
		<div class="print-container">
			<div class="row gutters">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
					<div class="card">
						<div class="card-body p-0">
							<div class="invoice-container">
								<div class="invoice-header">
									<!-- Row start -->
									<div class="row gutters">
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
										</div>
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" align="right">
											<img src="{{asset('/images/logoimage/logo.jpg')}}" alt="logo" style="padding-bottom:15px;">
										</div>
									</div>
									<!-- Row end -->
									<!-- Row start -->
									<div class="row gutters">
										<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4">
											<div align="left">
												<b>{{$from_name}}</b>
												<br>
												<b>{{$from_number}}</b>
												<br>
												<b>{{$from_address}}</b>
												<br>
												<b>{{$from_trnnumber}}</b>
												<br>
												<b>{{$from_email}}</b>
												<br>
											</div>
										</div>
										<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4" align="center">
										</div>
									</div>
									<!-- Row end -->
									<!-- Row start -->
									<div class="row gutters">
										<div class="col-lg-4 col-md-4 col-sm-4">
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
											<address class="text-left">
											</address>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-4">
										</div>
									</div>
									<!-- Row end -->
									<!-- Row start -->
									<div align="center">
										<h3 style="background-color:#ADD8E6;">{{$invoice_type}}</h3>
									</div>
									<br>
									<table class="heading">
										<tr>
											<td>
												<div align="left">
													<b>{{$to_name}}</b>
													<br>
													<b>{{$to_number}}</b>
													<br>
													<b>{{$to_address}}</b>
													<br>
													<b>{{$to_trnnumber}}</b>
													<br>
													<b>{{$to_email}}</b>
													<br>
													<br>
												</div>
											</td>
											<td>
												<div align="right">
													<address>
														@foreach($shopdatas as $shopdata)
														<br>
														<b>Date:</b> {{$date}}
														<br>
														<b>Supplied Date:</b> {{$supplieddate}}
														<br>
														<b>Due Date:</b> {{$due_date}}
														<br>
														<b>Invoice Number:</b> {{$invoice_number}}
														@endforeach
													</address>
												</div>
											</td>
										<tr>
											<hr style="border-top: dotted 2px; color:#ddd;" />
											</hr>
									</table>
									<!-- Row end -->
									<div align="center">
										<h3>{{$heading}}</h3>
									</div>
								</div>
								<div class="invoice-body">
									<!-- Row start -->
									<div class="row gutters">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="table-responsive">
												<table class="table custom-table m-0">
													<thead>
														<tr>
															<th>Sl</th>
															<th>Description</th>
															<!-- <th>Product ID</th> -->
															<th>Quantity</th>
															<th>Rate</th>
															<th>Amount</th>
															<th>{{$tax}}(%)</th>
															<th>{{$tax}} Amount</th>
															<th>Net Amount</th>
														</tr>
													</thead>
													<tbody>
														<!-- product details -->
														<?php $number = 1; ?>
														@foreach($details as $detail)
														<tr>
															<td>{{ $number }}</td>
															<td>{{$detail->product_name}}</td>
															<!-- <td>{{$detail->product_id}}</td> -->
															<td>{{$detail->quantity}}</td>
															<td>{{$detail->mrp}}</td>
															<td>{{$detail->price}}</td>
															<td>{{$detail->fixed_vat}}</td>
															<td>{{(($detail->fixed_vat*$detail->price)/100)}}</td>
															<td>{{(($detail->fixed_vat*$detail->price)/100)+$detail->price}}</td>
														</tr>
														<?php $number++; ?>
														@endforeach
														<!-- total -->
														<tr>
															<td colspan="6">
																<p>
																	<br>
																	<br>
																</p>
																<h5 class="text-success"><strong>Amount in Words : {{$amountinwords}}</strong></h5>
															</td>
															<td>
																<p>
																	Subtotal<br>
																	Tax<br>
																</p>
																<h5 class="text-success"><strong>Grand Total</strong></h5>
															</td>
															<td>
																<p>
																	{{$totals}}<br>
																	{{$vat}}<br>
																</p>
																<h5 class="text-success"><strong>{{$totals+$vat}} &nbsp{{$currency}}</strong></h5>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<!-- Row end -->
								</div>
								<div>
									Terms And Conditions
									<br>
									@php $termsnumber = 1; @endphp
									@foreach($termsandconditions as $termsandcondition)
									{{$termsnumber}}.{{$termsandcondition->termsandcondition}}
									<br>
									@php $termsnumber=$termsnumber+1; @endphp
									@endforeach
								</div>
								<div align="center">
									<h6>{{$footer}}</h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
