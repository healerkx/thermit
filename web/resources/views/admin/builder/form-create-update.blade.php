
@extends('admin.frame')

@section('content')
    <div class="block-area" id="text-input">
        <input class="form-control input-lg m-b-10" type="text" placeholder="Page Name" value="{{$pageName}}">

        <label>URL (Begin with http:// or https://)</label>
        <input value="{{$url}}" class="form-control input-lg m-b-10 validate[required,custom[url]]" type="text" placeholder="...">

        <!-- List columns settings -->
        <div id="field-table">
            <div class="tile">
                <h2 class="tile-title">List columns settings</h2>
                <div class="tile-config dropdown">
                    <a data-toggle="dropdown" href="" class="tooltips tile-menu" title="Options"></a>
                    <ul class="dropdown-menu pull-right text-right">
                        <li id="add-field-link"><a href="javascript:void(0)">Add New</a></li>
                    </ul>
                </div>

                <div class="listview sortable">
                    <div class="media field-template" style="overflow: inherit;display: none">

                        <div class="list-options">
                            <a href="javascript:void(0)" class="btn btn-sm btn-del"><i class="fa fa-trash-o"></i></a>
                        </div>

                        <div class="col-lg-2">
                            <input type="text" class="form-control m-b-4" placeholder="List column name">
                        </div>

                        <div class="col-lg-2">
                            <input type="text" class="form-control m-b-4" placeholder="Field name">
                        </div>

                        <div class="col-md-2 m-b-2">
                            <select class="select">
                                <option>Original</option>
                                <option>Datetime</option>
                                <option>Enum</option>
                                <option>Image</option>
                                <option>Link</option>

                            </select>
                        </div>
                        <div style="clear: both;overflow: hidden">
                        </div>
                    </div>
                    @foreach($fields as $field)
                    <div class="media" style="overflow: inherit;">

                        <div class="list-options">
                            <a href="javascript:void(0)" class="btn btn-sm btn-del"><i class="fa fa-trash-o"></i></a>
                        </div>

                        <div class="col-lg-2">
                            <input type="text" class="form-control m-b-4" placeholder="List column name" value="{{$field['columnName']}}">
                        </div>

                        <div class="col-lg-2">
                            <input type="text" class="form-control m-b-4" placeholder="Field name" value="{{$field['fieldName']}}">
                        </div>

                        <div class="col-md-2 m-b-2">
                            <select class="select">
                                <option @if ($field['fieldType'] == 'text') selected @endif>Original</option>
                                <option @if ($field['fieldType'] == 'datetime') selected @endif>Datetime</option>
                                <option @if ($field['fieldType'] == 'enum') selected @endif>Enum</option>
                                <option @if ($field['fieldType'] == 'image') selected @endif>Image</option>
                                <option @if ($field['fieldType'] == 'link') selected @endif>Link</option>

                            </select>
                        </div>
                        <div style="clear: both;overflow: hidden">
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        <input class="btn btn-sm" type="submit" value="Submit">

    </div>
@endsection

