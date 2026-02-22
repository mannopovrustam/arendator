@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" rel="stylesheet">

    <style>
        #contract-index{
            z-index: 1;
        }
        #contract-show{
            z-index: 2;
        }
        #contracts-table_filter{
            width: 600px !important;
        }
        #contracts-table_filter input{
            width: 500px !important;
        }
	table.dataTable tbody tr{
	    background-color: transparent;
	}
    </style>
    <style>
        .viewed {
            background-color: #2f3e66 !important;
            color: #ffffff !important;
        }
        table.dataTable tbody tr{
            background: transparent;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script>
        $(function () {
            $('#listok-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/backend/listok/data',
                columns: [
                    { data: 'fio', name: 'fio' },
                    { data: 'dtBirth', name: 'dtBirth' },
                    { data: 'psp', name: 'psp' },
                    { data: 'dtVisitOn', name: 'dtVisitOn' },
                    { data: 'dtVisitOff', name: 'dtVisitOff' },
                    { data: 'cad_number', name: 'cad_number' }
                ]
            });
        });
    </script>

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card" id="user">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            Ro'yhatga qo'yilganlar
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="btn-sm btn-link waves-effect waves-light ml-2"
                                    data-bs-toggle="modal" data-bs-target="#myModal"><i class="fa fa-filter"></i>
                            </button>
                            <!-- sample modal content -->
                            <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myModalLabel">Сана бўйича фильтр</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="form-group">
                                                    <p>Яратилган сана</p>
                                                    <div id="rgsrange1" class="form-control"
                                                         style="margin-top: 5px;background: #fff; cursor: pointer; border: 1px solid #55acee;display:inline!important;">
                                                        <i class="fa fa-calendar"></i>&nbsp;<span></span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="type">Mulk shakli</label>
                                                    <select class="form-select" name="type" id="type" required onchange="showJudge()">
                                                        <option value="" selected>*** Tanlash ***</option>
                                                        <option value="0">Jismoniy</option>
                                                        <option value="1">Yuridik</option>
                                                    </select>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" data-bs-dismiss="modal"
                                                    class="btn btn-primary waves-effect waves-light"
                                                    onclick="reloadTable()">OK
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex">
                            <a href="/contracts/create" class="btn btn-primary mr-3"><i class="fa fa-file-alt"></i> Янги ҳужжат</a>
                            @can('Ҳужжатларни ўчириш')
                                <a class="btn btn-danger" onclick="deleteSelected()"><i class="fa fa-trash-alt"></i> Ўчириш</a>
                            @endcan
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="over_lay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.5); z-index: 1000;"></div>
                        <div class="over_lay spinner-grow text-primary m-1" style="display: none; position: absolute; top: 50%; left: 50%; z-index: 1000;" role="status"><span class="sr-only">Loading...</span></div>
                        <table class="table" id="listok-table">
                            <thead>
                            <tr>
                                <th>FIO</th>
                                <th>Tug'ilgan sana</th>
                                <th>Pasport</th>
                                <th>Ro'yxatdan o'tish</th>
                                <th>Ro'yxatdan chiqarish</th>
                                <th>Kadastr</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="contract-show"></div>
@endsection
