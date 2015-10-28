<section class="panel">
    <header class="panel-heading">
        {{ $meta->edit->heading }}
    </header>
    <div class="panel-body">
        {!! Form::model($model, ['class' => 'form-horizontal bucket-form ajax-form', 'method' => 'put', 'data-target' => '#record-detail', 'route' => [$meta->edit->form->route, $record->id]]) !!}
            @foreach($meta->edit->form->fields as $fieldData)
            <div class="form-group">
                {!! Form::label($fieldData->name, $fieldData->label, ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    @if($fieldData->type == 'text')
                        {!! Form::text($fieldData->name, $fieldData->placeholder) !!}
                    @elseif($fieldData->type == 'checkbox')
                        {!! Form::checkbox($fieldData->name, 'true') !!}
                    @elseif($fieldData->type == 'textarea')
                        {!! Form::textarea($fieldData->name, $fieldData->placeholder) !!}
                    @endif
                </div>
            </div>
            @endforeach
            <button type="submit" class="btn btn-info">Save</button>
        {!! Form::close() !!}
    </div>
</section>