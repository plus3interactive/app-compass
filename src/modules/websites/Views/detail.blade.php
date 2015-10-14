		<section class="wrapper">
			<!-- page start-->
			<div class="row">
				<div class="col-sm-3">
					<section class="panel">
						<div class="panel-body">
							<ul class="nav nav-pills nav-stacked mail-nav">
								<li class="active"><a href="#" data-click="/cp/websites/{{ $record->id }}/edit" data-target="#website-detail"> <i class="fa fa-list"></i> Website Connection </a></li>
								<li><a href="#" data-click="/cp/websites/{{ $record->id }}/settings" data-target="#website-detail"> <i class="fa fa-list"></i> Website Configuration </a></li>
								@if($record->load('pages.template.sections'))

									@foreach($record->pages as $page)

											<li><a href="" data-click="/cp/websites/{{ $record->id }}/pages/{{ $page->id }}"  data-target="#main-content"><i class="fa fa-list"></i>{{ ucfirst($page->name) }}</a></li>

									@endforeach

								@endif
							</ul>
						</div>
					</section>


					<section class="panel">
						<div class="panel-body">
							<ul class="nav nav-pills nav-stacked labels-info ">
								<li> <h4>Recent Website Activity</h4> </li>
								<li> <a href="#"> <i class="fa fa-comments-o text-success"></i> 08-10-2015 03:12:46 PM <p>something happened</p></a>  </li>
								<li> <a href="#"> <i class="fa fa-comments-o text-danger"></i> 08-11-2015 03:12:46 PM <p>something else happened</p></a> </li>
								<li> <a href="#"> <i class="fa fa-comments-o text-muted "></i> 08-12-2015 03:12:46 PM <p>another thing that a manager should pay attention to and this text should contain the details</p></a></li>
	{{-- 							<li> <a href="#"> <i class="fa fa-comments-o text-muted "></i> YGL Sync - New users<span class="label label-success pull-right inbox-notification">9</span><p>Sync operation successful with 9 new addresses</p></a></li>
								<li> <a href="#"> <i class="fa fa-comments-o text-muted "></i> YGL Sync - Off Market users<span class="label label-danger pull-right inbox-notification">3</span><p>Sync operation successful with 3 users taken off market</p></a></li>
								<li> <a href="#"> <i class="fa fa-comments-o text-muted "></i> YGL Sync - On Market users<span class="label label-info pull-right inbox-notification">85</span><p>Sync operation successful with 85 users brought on market</p></a></li>
	 --}}						</ul>
							{{-- <a href="#"> + Add More</a> --}}

							{{-- <div class="inbox-body text-center inbox-action">
								<div class="btn-group">
									<a class="btn mini btn-default" href="javascript:;">
										<i class="fa fa-power-off"></i>
									</a>
								</div>
								<div class="btn-group">
									<a class="btn mini btn-default" href="javascript:;">
										<i class="fa fa-cog"></i>
									</a>
								</div>
							</div> --}}
						</div>
					</section>
				</div>
				<div class="col-sm-9" id="website-detail" data-load="/cp/websites/{{ $record->id }}/edit"></div>
			</div>

		<!-- page end-->
		</section>