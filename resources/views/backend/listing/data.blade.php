<style>
    .viewed {
        background-color: #2f3e66 !important;
        color: #ffffff !important;
    }
    .selected {
        background-color: #2f3e66 !important;
        color: #ffffff !important;
    }
</style>
<script>

    var table = null;
    var budget = null;
    var stopLoop = false;


    /*
        var start_ins = moment().subtract(7, 'years').startOf('year');
        var end_ins = moment().add(1, 'days');
    */
    // var start_ins = moment().subtract(7, 'years').startOf('year');
    // var end_ins = moment().add(1, 'days');
    // var start_created = moment().subtract(7, 'years').startOf('year');
    // var end_created = moment().add(1, 'days');

    $(document).ready(function () {

        table = $('#drugs-table').DataTable({
            processing: true,
            scrollY: "55vh",
            serverSide: true,
            stateSave: true,
            lengthMenu: [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],
            pageLength: 50,
            rowId: 'id',
            columnDefs: [
                // {orderable: false, targets: 0}, // Disable ordering for columns 2 and 4
                {searchable: false, targets: 0} // Disable searching for columns 3 and 5
            ],
            order: [[0, 'asc']],
            select: {style: 'os', selector: 'td:first-child'},
            ajax: {
                url: "/backend/listings/data-table",
                type: "GET"
            },
            columns: [
                {data: 'id'},
                {data: 'address'},
                {data: 'type_name'},
                {data: 'region_name'},
                {data: 'category_name'},
                {data: 'district_name'},
                {data: 'rooms_qty'},
            ]
        });

    });

    $('#drugs-table').on('processing.dt', function (e, settings, processing) {
        if (processing)
            $('.ajaxLoading').show();
        else
            $('.ajaxLoading').hide();
    });
    $('#check_all').on('change', function () {
        $('.viewed').removeClass('viewed');
        if ($('#check_all').prop('checked')) $('#drugs-table tbody tr').addClass('selected');
        else $('#drugs-table tbody tr').removeClass('selected');
    });

    $('#drugs-table tbody').on('click', 'td', function () {
        $('.viewed').removeClass('viewed');
        var cell = table.cell(this);
        if (cell.index().column != 1)
            $(this).closest('tr').toggleClass('selected');
    });

    function getSelected() {
        var total = table.rows('.selected').count();

        if (total == 0) {
            $.alert({
                icon: 'fa fa-info',
                closeIcon: true,
                type: 'red',
                title: '&nbsp;Дори танланмади!',
                content: '<br>Aввал сичқончанинг чап тугмаси билан чизиқни босиш орқали дорини танланг!',
                columnClass: 'small',
            });
            return false;
        }
        return true;
    }


    function updateSelected() {
        if (!getSelected()) return false;

        var id = table.rows('.selected').data()[0].id;
        window.location.href = '/backend/listings/' + id + '/edit';
    }

    $('#drugs-table tbody').on('dblclick', 'tr', function () {
        showSingle(this.id);
    });

    function showSingle(id){
        $('.viewed').removeClass('viewed');
        $(this).toggleClass('viewed');
        $('#drug-index').hide();
        $('#drug-show').show();
        $('#drug-show').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
        $('#drug-show').load('/backend/listings/' + id);
    }

    // Function to format the number with commas as thousands separators
    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="user">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        E'lonlar
                    </div>

                    <div class="d-flex">
                        <a href="/backend/listings/create" class="btn btn-primary mr-3"><i class="fa fa-plus-square"></i>
                            Yangi e'lon
                        </a>
                        @can('drug-delete')
                            <a class="btn btn-danger" onclick="deleteSelected()"><i class="fa fa-trash-alt"></i>O‘chirish</a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <table class="table" id="drugs-table">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>address</th>
                            <th>type_name</th>
                            <th>region_name</th>
                            <th>category_name</th>
                            <th>district_name</th>
                            <th>rooms_qty</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
