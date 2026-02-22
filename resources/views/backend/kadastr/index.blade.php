@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>

    <style>
        #signature-index{
            z-index: 1;
        }
        #signature-show{
            z-index: 2;
        }
        #kadastr-table_filter{
            width: 600px !important;
        }
        #kadastr-table_filter input{
            width: 500px !important;
        }
	table.dataTable tbody tr{
	    background-color: transparent;
	}
    </style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="{{ asset('/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
    {{--<script>
        $(document).ready(function (){
            $('#signature-index').load('{{ asset('kadastr/data') }}');
        })
    </script>--}}
    <script>

        var table = null;
        var budget = null;
        var stopLoop = false;

        $(document).ready(function () {

            table = $('#kadastr-table').DataTable({
                processing: true,
                scrollY: "55vh",
                serverSide: true,
                lengthMenu: [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],
                pageLength: 50,
                rowId: 'id',
                columnDefs: [
                    {orderable: false, targets: 0}, // Disable ordering for columns 2 and 4
                    {searchable: false, targets: 0} // Disable searching for columns 3 and 5
                ],
                order: [[1, 'desc']],
                select: {style: 'os', selector: 'td:first-child'},
                ajax: {
                    url: "/backend/kadastr/data",
                    type: "GET"
                },

                columns: [
                    {data:'id',name:'id',sClass:'dt-center'},
                    {data:'name',name:'name'},{data:'cad_number',name:'cad_number'},
                    {data:'object_type',name:'object_type'},{data:'address',name:'address'},
                    {data:'commerce',name:'commerce',sClass:'dt-center'},{data:'st',name:'st',sClass:'dt-center'}
                ]
            });

            $('#kadastr-table_filter input').unbind();
            $('#kadastr-table_filter input').bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    table.search(this.value).draw();
                    $('#kadastr-table_filter input').blur();
                }
            });
        });

        $('#kadastr-table').on('processing.dt', function (e, settings, processing) {
            if (processing) $('.over_lay').show();
            else $('.over_lay').hide();
        });
        $('#check_all').on('change', function () {
            $('.viewed').removeClass('viewed');
            if ($('#check_all').prop('checked')) $('#kadastr-table tbody tr').addClass('selected');
            else $('#kadastr-table tbody tr').removeClass('selected');
        });
        $('#kadastr-table tbody').on('click', 'td', function () {
            $('.viewed').removeClass('viewed');
            $('#kadastr-table tbody tr').removeClass('selected');
            $(this).closest('tr').toggleClass('selected');
        });

        function reloadTable() {
            table.ajax.reload();
        }

        function getSelected() {
            var total = table.rows('.selected').count();

            if (total == 0) {
                $.alert({
                    icon: 'fa fa-info',
                    closeIcon: true,
                    type: 'red',
                    title: '&nbsp;Ҳужжат танланмади!',
                    content: '<br>Aввал сичқончанинг чап тугмаси билан чизиқни босиш орқали ҳужжатни танланг!',
                    columnClass: 'small',
                });
                return false;
            }
            return true;
        }

    </script>
@endsection

@section('content')
    <style>
        .viewed {
            background-color: #2f3e66 !important;
            color: #ffffff !important;
        }
        table.dataTable tbody tr{
            background: transparent;
        }
    </style>



    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" id="user">
                    <div class="card-header d-flex justify-content-between">
                        <div>Kadastrlarim</div>
                    </div>

                    <div class="card-body">
                        <div class="over_lay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5); z-index: 1000;"></div>
                        <div class="over_lay spinner-grow text-primary m-1" style="display: none; position: absolute; top: 50%; left: 50%; z-index: 1000;" role="status"><span class="sr-only">Loading...</span></div>
                        <table class="table" id="kadastr-table">
                            <thead>{!! $tableTH !!}</thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
