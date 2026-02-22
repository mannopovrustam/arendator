@extends('layouts.app')

@section('style')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" rel="stylesheet"
          type="text/css"/>

    <style>
        .input-group-text:hover {
            color: #fff;
            background: red;
            transition: all 0.3s;
        }
        .person-info{
            display: none;
        }
    </style>
@endsection
@section('script')
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <script>
        function selectCadastr(cadastrs) {

            let list = '';
            cadastrs.forEach(v => {
                list += `
                    <div class="mb-1 d-flex align-items-center justify-content-between">
                        <input class="btn-check" type="radio" name="cadastr" id="selectCad${v.id}" value="${v.id}" data-cad="${v.cad_number}" data-payload='${JSON.stringify(v)}'>
                        <label class="btn btn-outline-secondary" style="text-align: left" for="selectCad${v.id}"><b>${v.cad_number}</b> <br><small><i>${v.address}</i></small></label>
                    </div>
                `;
            });

            $.confirm({
                title: 'Kadastr tanlang',
                content: `<div id="cadastrForm">${list}</div>`,
                onContentReady: function () {
                    let jc = this; // confirm instance
                    $('#cadastrForm').on('change', 'input[name="cadastr"]', function () {
                        let el = $(this);

                        let selected = el.data('cad');
                        let payload  = el.attr('data-payload');
                        payload = JSON.parse(payload);

                        renderSelectedCadastr(selected, payload);
                        jc.close(); // ✅ CONFIRM OYNANI YOPISH
                        // agar modalni avtomatik yopmoqchi bo‘lsangiz:
                        // this.close();
                    });
                },
                buttons: {
                    bekor: function () {}
                },
                /*
                                buttons: {
                                    tanlash: {
                                        text: 'Tanlash',
                                        btnClass: 'btn-primary',
                                        action: function () {
                                            let selected = $('#cadastrForm input[name="cadastr"]:checked').data('cad');
                                            let selected_payload = $('#cadastrForm input[name="cadastr"]:checked').data('payload');
                                            if (!selected) {
                                                $.alert('Kadastr tanlang!');
                                                return false;
                                            }
                                            renderSelectedCadastr(selected,selected_payload);
                                        }
                                    },
                                    bekor: function () {}
                                }
                */
            });
        }

        function renderSelectedCadastr(cadastr, payload) {
            $('#cadastr-cell').html(`
        <div class="d-flex align-items-center gap-2">
            <b>${cadastr},</b> <i>${payload.address}</i>
            <abbr title="Almashtirish"><button class="btn btn-sm btn-warning" type="button" onclick="changeCadastr()" aria-label="Almashtirish"><i class="fas fa-retweet"></i></button></abbr>
            <abbr title="Tozalash"><button class="btn btn-sm btn-danger" type="button" onclick="clearCadastr()" aria-label="Tozalash"><i class="fa fa-trash"></i></button></abbr>
            <input type="hidden" name="cadastr" value="${payload.id}">
        </div>
    `);
        }

        function changeCadastr() {
            selectCadastr(<?php echo json_encode($cadastrs); ?>);
        }

        function clearCadastr() {
            var cadastrs = <?php echo json_encode($cadastrs); ?>;
            $('#cadastr-cell').html(`
        <button type="button" class="btn btn-primary" onclick="selectCadastr({{json_encode($cadastrs)}})">
            Kadastr tanlash
        </button>
    `);
        }
    </script>

    <script>
        function findPerson(){
            let passport = $('#passport').val();
            let birthday = $('#birthday').val();

            if(!passport){
                alert('Iltimos, pasport ma\'lumotlarini kiriting!');
                return;
            }
            if(!birthday){
                alert('Iltimos, tug\'ilgan sanani kiriting!');
                return;
            }

            $.ajax({
                url: '/backend/person-info',
                method: 'POST',
                data: {
                    passport: passport,
                    birthday: birthday,
                    ch_info: '{{ md5('silkroad_emehmon' . date('YmdH') . 'pspdtb') }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.psp){
                        let psp = response.psp;

                        $('#pinfl').val(psp.Pinpp);
                        $('#surname').val(psp.surname);
                        $('#firstname').val(psp.firstname);
                        $('#lastname').val(psp.lastname);
                        $('#dtBirth').val(psp.datebirth);
                        $('#sex').val(psp.sex);
                        $('#pspDate').val(psp.datePassport);
                        $('#pspIssuedBy').val(psp.PassportIssuedBy);
                        $('#fullname').html(`<b>${psp.surname} ${psp.firstname} ${psp.lastname}</b>`);
                        $('#dbirth').html(psp.datebirth);
                        $('#gender').html(psp.sex === 'M' ? 'Erkak' : 'Ayol');
                        $('.person-info').show();
                        $('#passport').attr('readonly', true);
                        $('#birthday').attr('readonly', true);

                        $('.find').hide();
                        $('.refind').show();

                    } else {
                        alert('Foydalanuvchi topilmadi. Iltimos, ma\'lumotlarni tekshiring va qayta urinib ko\'ring.');
                    }

                },
                error: function() {
                    // alert('Xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.');
                }
            });

        }

        function refind(){
            $('#pinfl').val('');
            $('#surname').val('');
            $('#firstname').val('');
            $('#lastname').val('');
            $('#dtBirth').val('');
            $('#sex').val('');
            $('#pspDate').val('');
            $('#pspIssuedBy').val('');
            $('#fullname').html('');
            $('#dbirth').html('');
            $('#gender').html('');
            $('.person-info').hide();
            $('#passport').attr('readonly', false);
            $('#birthday').attr('readonly', false);
            $('.find').show();
            $('.refind').hide();
        }

        // dtVisitOn dtVisitOff after filling calculate days and show in Kunlar soni
        $('#dtVisitOn, #dtVisitOff').on('change', function() {
            let dtVisitOn = $('#dtVisitOn').val();
            let dtVisitOff = $('#dtVisitOff').val();


            if(dtVisitOn && dtVisitOff){
                let date1 = new Date(dtVisitOn);
                let date2 = new Date(dtVisitOff);
                let timeDiff = Math.abs(date2.getTime() - date1.getTime());
                let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end date
                // date1 less than date2 and more than today
                let today = new Date();
                if(date1 <= today){
                    $.alert('Ro\'yhatga qo\'yish sanasi bugungi kundan kichik bo\'lishi mumkin emas!');
                    $('#dtVisitOn').val('');
                    $('#dtVisitOff').val('');
                    $('#wdays').val('');
                    $('#wdays_txt').html('');
                    return;
                }

                // show in Kunlar soni
                $('#wdays').val(diffDays);
                $('#wdays_txt').html(diffDays);
            }
        });

    </script>

@endsection
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">


                <div class="card" id="user">
                    <div class="card-header d-flex justify-content-between">
                        <p>Регистрация</p>
                        <a href="/backend/listok" class="btn btn-primary"><i
                                    class="uil-left-arrow-from-left"></i> Ortga</a>
                    </div>
                    <div class="card-body">
                        <form action="/backend/listok" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="pinfl" id="pinfl">
                                <input type="hidden" name="surname" id="surname">
                                <input type="hidden" name="firstname" id="firstname">
                                <input type="hidden" name="lastname" id="lastname">
                                <input type="hidden" name="dtBirth" id="dtBirth">
                                <input type="hidden" name="sex" id="sex">
                                <input type="hidden" name="pspDate" id="pspDate">
                                <input type="hidden" name="pspIssuedBy" id="pspIssuedBy">
                                <input type="hidden" name="wdays" id="wdays">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <label for="passport" class="form-label mb-0">Pasport ma’lumotlari</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="passport" name="passport" placeholder="ZZ0011223" value="ZZ0011223">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="dtb" class="form-label mb-0">Tug‘ilgan sanasi</label></td>
                                        <td>
                                            <input type="date" class="form-control" id="birthday" name="birthday" value="2004-09-23">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-primary find"
                                                    onclick="findPerson()">
                                                <i class="fa fa-search"></i> Izlash
                                            </button>
                                            <button type="button"
                                                    class="btn btn-warning refind"
                                                    onclick="refind()" style="display: none;">
                                                <i class="fa fa-search"></i> Qayta izlash
                                            </button>
                                        </td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table class="table table-sm table-hover table-bordered table-striped align-middle person-info">
                                    <tbody>
                                    <tr>
                                        <td class="label-dark-blue text-left"><label>Mehmon</label></td>
                                        <td style="font-weight: 400;color:#222;" id="fullname"><b>Fayziyev Davron</b></td>
                                    </tr>
                                    <tr>
                                        <td class="label-view text-leftt"><label>Tug'ilgan sana</label></td>
                                        <td style="font-weight: 400;color:#222;" id="dbirth">23-09-2004 </td>
                                    </tr>
                                    <tr>
                                        <td class="label-view text-left"><label>Jinsi</label></td>
                                        <td style="font-weight: 400;color:#222;" id="gender">Erkak </td>
                                    </tr>
                                    <tr>
                                        <td class="label-view text-left"><label>Ro'yhatga qo'yish muddati</label></td>
                                        <td style="font-weight: 400;color:#222;display: flex; align-items: center">
                                            <input type="date" class="form-control form-control-sm" name="dtVisitOn" id="dtVisitOn"> <i class="fa fa-minus" style="margin: 0 5px;"></i>
                                            <input type="date" class="form-control form-control-sm" name="dtVisitOff" id="dtVisitOff">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-view text-left"><label>Kunlar soni</label></td>
                                        <td style="font-weight: 400;color:#222;" id="wdays_txt"></td>
                                    </tr>

                                    </tbody>
                                </table>
                                <table class="table-sm table-hover table-bordered table-striped align-middle person-info">
                                    <tbody>
                                        @if(auth()->user()->one_id)
                                            <tr id="cadastr-row">
                                                <td id="cadastr-cell">
                                                    <button type="button" class="btn btn-primary" onclick="selectCadastr({{ json_encode($cadastrs) }})">
                                                        Kadastr tanlash
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <div class="form-group mt-3 d-flex justify-content-end align-items-end">
                                    <button type="submit" class="btn btn-success">Saqlash</button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
