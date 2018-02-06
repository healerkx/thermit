
@extends('admin.frame')

@section('content')

    <!-- TODO: filters -->
    @foreach($filters as $filter)
        <div class="media" style="overflow: inherit;">

            <div class="list-options">
                <a href="" class="btn btn-sm"><i class="fa fa-trash-o"></i></a>
            </div>

            <div class="col-lg-2">
                <input type="text" class="form-control m-b-4" placeholder="List column name" value="{{$filter['label']}}">
            </div>

            <div class="col-lg-2">
                <input type="text" class="form-control m-b-4" placeholder="Field name" value="{{$filter['paramName']}}">
            </div>

            <div class="col-md-2 m-b-2">
                <select class="select">
                    <option>Text</option>
                    <option>Datetime Range</option>
                    <option>Enum</option>
                </select>
            </div>
            <div style="clear: both;overflow: hidden">
            </div>
        </div>
    @endforeach


    <div class="block-area" id="tableStriped">
        <h3 class="block-title">Table Striped</h3>
        <div class="table-responsive overflow">
            <table class="tile table table-bordered table-striped">
                <thead>
                <tr>
                    @foreach($fields as $field)
                    <th>{{$field['columnName']}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                <tr>
                    @foreach($fields as $field)
                        @if($field['fieldType'] == 'text')
                            <td>{{$item[$field['fieldName']]}}</td>
                        @elseif($field['fieldType'] == 'datetime')
                            <td>{{date('Y-m-d H:i:s', $item[$field['fieldName']])}}</td>
                        @elseif($field['fieldType'] == 'link')
                            <td><a href="{{$item[$field['fieldName']]}}">{{$item[$field['fieldName']]}}</a></td>
                        @elseif($field['fieldType'] == 'image')
                            <td><img src="{{$item[$field['fieldName']]}}"/></td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
