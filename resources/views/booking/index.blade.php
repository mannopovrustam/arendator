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
        #booking-table_filter{
            width: 600px !important;
        }
        #booking-table_filter input{
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
            $('#signature-index').load('{{ asset('booking/data') }}');
        })
    </script>--}}
    <script>

        var table = null;
        var budget = null;
        var stopLoop = false;

        $(document).ready(function () {

            table = $('#booking-table').DataTable({
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
                    url: "/bookings/data",
                    type: "GET"
                },

                columns: [
                    {data:'id',name:'b.id',sClass:'dt-center'},{data:'listing_name',name:'l.name'},{data:'staffname',name:'b.staffname'},{data:'period'},{data:'created_at',name:'b.created_at'},{data:'status',sClass:'dt-center'}
                ]
            });

            $('#booking-table_filter input').unbind();
            $('#booking-table_filter input').bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    table.search(this.value).draw();
                    $('#booking-table_filter input').blur();
                }
            });
        });

        $('#booking-table').on('processing.dt', function (e, settings, processing) {
            if (processing) $('.over_lay').show();
            else $('.over_lay').hide();
        });
        $('#check_all').on('change', function () {
            $('.viewed').removeClass('viewed');
            if ($('#check_all').prop('checked')) $('#booking-table tbody tr').addClass('selected');
            else $('#booking-table tbody tr').removeClass('selected');
        });
        $('#booking-table tbody').on('click', 'td', function () {
            $('.viewed').removeClass('viewed');
            $('#booking-table tbody tr').removeClass('selected');
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
                    title: '&nbsp;Diqqat!',
                    content: '<br>Avval sichqon yordamida biror bronni tanlang!',
                    columnClass: 'small',
                });
                return false;
            }
            return true;
        }

        function setPropiska(){

            // not selected
            if(!getSelected()) return;
            var id = table.row('.selected').id();
            // if selected row data-one_id is empty
            var one_id = table.row('.selected').data().one_id;
            if (!one_id){
                $.alert({
                    icon: 'fa fa-info',
                    closeIcon: true,
                    type: 'blue',
                    title: '&nbsp;Diqqat!',
                    content: '<br>Tanlangan mijoz ONEID tizimi orqali ro\'yhatdan o\'tmagan!',
                    columnClass: 'small',
                    backgroundDismiss: true,
                    escapeKey: true,
                    buttons: {}
                });
                return;
            }

            $.confirm({
                title: id + ' Ro\'yhatga qo\'yish',
                closeIcon: true,
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<table class="table table-bordered">' +
                    '<tr><th>Boshlanish sanasi</th><th>Tugash sanasi</th></tr><tr>' +
                    '<td><input type="date" class="form-control" id="date_from" name="date_from" min="{{ date('Y-m-d') }}" required /></td>' +
                    '<td><input type="date" class="form-control" id="date_to" name="date_to" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required /></td></tr>' +
                    '<td colspan="2"><input type="text" class="form-control comment" id="comment" placeholder="Izoh" name="comment" /></td>' +
                    '</div>' +
                    '</form>',
                columnClass: 'medium',
                buttons: {
                    formSubmit: {
                        text: 'Qo\'yish',
                        btnClass: 'btn-blue',
                        action: function () {
                            var comment = this.$content.find('#comment').val();
                            if(!comment){
                                $.alert('Iltimos, izoh kiriting!');
                                return false;
                            }
                            // date fields required
                            var date_from = this.$content.find('#date_from').val();
                            var date_to = this.$content.find('#date_to').val();
                            if(!date_from || !date_to){
                                $.alert('Iltimos, boshlanish va tugash sanalarini kiriting!');
                                return false;
                            }
                            if (date_from > date_to){
                                $.alert('Iltimos, boshlanish sanasi tugash sanasidan katta bo\'lmasin!');
                                return false;
                            }
                            if (date_from < '{{ date('Y-m-d') }}'){
                                $.alert('Iltimos, boshlanish sanasi joriy sanadan katta bo\'lsin!');
                                return false;
                            }
                            if (!id){
                                $.alert('Iltimos, avval bronni tanlang!');
                                return false;
                            }

                            $.ajax({
                                url: '/bookings/set-propiska',
                                type: 'get',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    propiska: name,
                                    date_from: date_from,
                                    date_to: date_to,
                                    booking_id: id
                                },
                                success: function (response) {
                                    var data;
                                    if (response.responseJSON) data = response.responseJSON;
                                    else data = response;

                                    if(data.status = 'success'){
                                        $.alert({
                                            icon: 'fa fa-check',
                                            closeIcon: true,
                                            type: 'green',
                                            title: '&nbsp;Muvaffaqiyatli!',
                                            content: '<br>'+data.message,
                                            columnClass: 'small',
                                        });
                                    } else {
                                        $.alert({
                                            icon: 'fa fa-info',
                                            closeIcon: true,
                                            type: 'red',
                                            title: '&nbsp;Xatolik!',
                                            content: '<br>'+data.message,
                                            columnClass: 'small',
                                        });
                                    }
                                },
                                error: function (response) {
                                    var data;
                                    if (response.responseJSON) data = response.responseJSON;
                                    else data = response;

                                    $.alert({
                                        icon: 'fa fa-info',
                                        closeIcon: true,
                                        type: 'red',
                                        title: '&nbsp;Xatolik!',
                                        content: data.message,
                                        columnClass: 'small',
                                    });
                                }
                            });
                        }
                    },
                    // hide cancel button
                },
                onContentReady: function () {
                    var jc = this;
                    this.$content.find('form').on('submit', function (e) {
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click');
                    });
                }
            });
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
                        <div>Tushgan bronlar</div>
                        <div>
                            <button class="btn btn-sm btn-primary" onclick="setPropiska()"><i class="fa fa-user-edit"></i> Ro'yhatga qo'yish</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="over_lay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5); z-index: 1000;"></div>
                        <div class="over_lay spinner-grow text-primary m-1" style="display: none; position: absolute; top: 50%; left: 50%; z-index: 1000;" role="status"><span class="sr-only">Loading...</span></div>
                        <table class="table table-hover" id="booking-table">
                            <thead>{!! $tableTH !!}</thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
