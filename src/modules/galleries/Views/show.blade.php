<section class="wrapper">
    <!-- page start-->
    <div class="row">
        <div class="col-sm-3">
            <section class="panel">
                <div class="panel-body">
                    <h4>Sub Sections</h4>
                    <ul class="nav nav-pills nav-stacked mail-nav">
                        @foreach($subnav as $subnav_name => $subnav_content)
                        <li>
                            <a {!! inlineAttrs($subnav_content['attributes']) !!}>
                                <i class="fa {{ $subnav_content['icon'] }}"></i>
                                <span>{{ $subnav_content['label'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            @include('alerts::alerts')

        </div>
        <div class="col-sm-9" id="record-detail" data-load="/galleries/{{ $record->id }}/edit"></div>
    </div>
<!-- page end-->
</section>