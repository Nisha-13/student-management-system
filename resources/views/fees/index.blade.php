@extends('layouts.app')

@section('title', 'Fee Management - Student Management System')
@section('page-title', 'Fee Collection & Dues')

@section('content')
<div id="feeAlertContainer"></div>

<div class="card card-custom shadow-sm border-0">

    <div class="card-body p-3 p-md-4">

        <!-- Header -->

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>

                <h4 class="fw-bold mb-1">
                    Fee Management
                </h4>

                <small class="text-muted">
                    Generate fee demands, track payments and review balances
                </small>

            </div>

            <button
                type="button"
                class="btn btn-primary rounded-pill px-4 add-fee-btn"
                data-bs-toggle="modal"
                data-bs-target="#createFeeModal">

                <i class="bi bi-plus-lg me-1"></i>

                Generate Fee Demand

            </button>

        </div>

        <!-- Table -->

        <div class="table-responsive fees-table-wrapper">

            <table
                id="feesTable"
                class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>Roll #</th>
                    <th>Student Name</th>
                    <th>Class / Section</th>
                    <th>Title</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Due Balance</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
                </table>

        </div>

    </div>

</div>

<!-- Modal: Generate Fee Demand -->
<div class="modal fade" id="createFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createFeeForm">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Generate Student Fee Demand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalAlertError" class="alert alert-danger d-none mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Select Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Choose Student...</option>
                            @foreach($classes as $cls)
                                @foreach($cls->students as $stu)
                                    <option value="{{ $stu->id }}">{{ $stu->user->name }} (Roll: {{ $stu->roll_number }} - {{ $cls->name }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Fee Description / Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Q1 Tuition Fee" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Amount ($) *</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control" placeholder="500.00" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small text-muted">Due Date *</label>
                            <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveFeeBtn" class="btn btn-primary fw-semibold">Generate Demand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="paymentForm">
                <input type="hidden" id="payFeeId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Total Paid Amount ($)</label>
                        <input type="number" step="0.01" min="0" id="payAmountInput" class="form-control" required>
                        <small class="text-muted d-block mt-1">Total Fee Amount: $<span id="payTotalLabel"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="savePayBtn" class="btn btn-success fw-semibold">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')

<style>

.card-custom{

    border-radius:16px;

}

/*--------------------------------------------------------------
# Button
--------------------------------------------------------------*/

.add-fee-btn{

    white-space:nowrap;

}

@media(max-width:767px){

.add-fee-btn{

    width:100%;

}

}

/*--------------------------------------------------------------
# Table
--------------------------------------------------------------*/

.fees-table-wrapper{

    overflow-x:auto;

    overflow-y:hidden;

    -webkit-overflow-scrolling:touch;

}

#feesTable{

    width:100%!important;

    min-width:1000px;

    table-layout:auto;

}

#feesTable thead th{

    font-weight:600;

    white-space:nowrap;

}

#feesTable td{

    white-space:nowrap;

    vertical-align:middle;

}

/* Action Column */

#feesTable th:last-child,
#feesTable td:last-child{

    min-width:150px;

    text-align:center;

    white-space:nowrap;

}

/*--------------------------------------------------------------
# Modal
--------------------------------------------------------------*/

.modal .form-control,
.modal .form-select{

    font-size:.95rem;

}

.modal .form-label{

    margin-bottom:.35rem;

}

/*--------------------------------------------------------------
# Scrollbar
--------------------------------------------------------------*/

.fees-table-wrapper::-webkit-scrollbar{

    height:8px;

}

.fees-table-wrapper::-webkit-scrollbar-thumb{

    background:#c8c8c8;

    border-radius:10px;

}

.fees-table-wrapper::-webkit-scrollbar-track{

    background:#efefef;

}

/*--------------------------------------------------------------
# Mobile
--------------------------------------------------------------*/

@media(max-width:576px){

.card-custom h4{

    font-size:1.1rem;

}

.card-custom h5{

    font-size:1.05rem;

}

.card-custom small{

    font-size:.80rem;

}

#feesTable th,
#feesTable td{

    font-size:.86rem;

    padding:.55rem;

}

.modal-dialog{

    margin:.75rem;

}

.modal-footer{

    display:flex;

    flex-direction:column;

    gap:.5rem;

}

.modal-footer .btn{

    width:100%;

}

}

/*--------------------------------------------------------------
# Tablet
--------------------------------------------------------------*/

@media(min-width:577px) and (max-width:991px){

#feesTable th,
#feesTable td{

    font-size:.92rem;

}

}

</style>

@endpush

@push('scripts')
<script>

$(function () {


    $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });



    const table = $('#feesTable').DataTable({

        processing: true,

        responsive:false,

        scrollX:true,

        autoWidth:false,

        deferRender:true,


        ajax:"{{ route('fees.index') }}",


        columns:[


            {data:'roll_number'},


            {
                data:'student_name',

                render:function(data){

                    return `<span class="fw-semibold">${data}</span>`;

                }

            },


            {data:'class_section'},


            {data:'title'},


            {data:'amount'},


            {data:'paid_amount'},


            {

                data:'due_balance',

                render:function(data){

                    return `<span class="text-danger fw-semibold">${data}</span>`;

                }

            },


            {data:'due_date'},


            {data:'status'},


            {

                data:'actions',

                orderable:false,

                searchable:false,

                className:'text-center'

            }


        ],


        pageLength:10,


        order:[[0,'asc']],


        language:{


            search:"_INPUT_",

            searchPlaceholder:"Search fee records...",


            lengthMenu:"Show _MENU_ records",


            info:"Showing _START_ to _END_ of _TOTAL_ records",


            zeroRecords:"No matching records found",


            emptyTable:"No fee records available"


        },


        initComplete:function(){

            this.api().columns.adjust();

        }


    });





    $(window).on('resize', function(){

        table.columns.adjust();

    });





    function showAlert(message,type='success'){


        let html=`


        <div class="alert alert-${type} alert-dismissible fade show mb-4">


            <i class="bi ${type==='success'
            ?'bi-check-circle-fill text-success'
            :'bi-exclamation-triangle-fill text-danger'} me-2"></i>


            <strong>${message}</strong>


            <button class="btn-close" data-bs-dismiss="alert"></button>


        </div>


        `;


        $('#feeAlertContainer').html(html);


    }







    $('#createFeeForm').submit(function(e){


        e.preventDefault();



        let btn=$('#saveFeeBtn');


        btn.prop('disabled',true)

        .html('<span class="spinner-border spinner-border-sm"></span> Processing...');



        $.post("{{route('fees.store')}}",$(this).serialize())


        .done(function(res){


            if(res.success){



                bootstrap.Modal
                .getInstance(
                    document.getElementById('createFeeModal')
                )
                .hide();



                $('#createFeeForm')[0].reset();



                table.ajax.reload(null,false);



                showAlert(res.message ?? 'Fee demand generated successfully');


            }


        })


        .fail(function(xhr){


            let msg='Failed to generate fee demand';



            if(xhr.responseJSON?.errors){


                msg=Object.values(xhr.responseJSON.errors)
                .flat()
                .join('<br>');


            }



            $('#modalAlertError')
            .removeClass('d-none')
            .html(msg);



        })


        .always(function(){


            btn.prop('disabled',false)
            .html('Generate Demand');


        });



    });







    $(document).on('click','.edit-payment-btn',function(){



        let id=$(this).data('id');

        let paid=$(this).data('paid');

        let amount=$(this).data('amount');



        $('#payFeeId').val(id);


        $('#payAmountInput')
        .val(paid)
        .attr('max',amount);



        $('#payTotalLabel').text(amount);



        new bootstrap.Modal(
            document.getElementById('paymentModal')
        ).show();



    });







    $('#paymentForm').submit(function(e){


        e.preventDefault();



        let amount=parseFloat($('#payAmountInput').val());

        let max=parseFloat($('#payAmountInput').attr('max'));



        if(amount > max){


            alert('Paid amount cannot exceed total fee amount');


            return;


        }




        let btn=$('#savePayBtn');


        btn.prop('disabled',true)
        .html('Saving...');




        $.ajax({


            url:"/fees/"+$('#payFeeId').val()+"/payment",


            type:"PUT",


            data:{


                paid_amount:amount


            }


        })


        .done(function(res){


            if(res.success){


                bootstrap.Modal
                .getInstance(
                    document.getElementById('paymentModal')
                )
                .hide();



                table.ajax.reload(null,false);



                showAlert(res.message ?? 'Payment recorded successfully');


            }



        })


        .fail(function(xhr){


            alert(xhr.responseJSON?.message ?? 'Payment error');


        })


        .always(function(){


            btn.prop('disabled',false)
            .html('Save Payment');


        });



    });








    $(document).on('click','.delete-fee-btn',function(){


        let id=$(this).data('id');



        if(!confirm('Delete this fee demand record?'))
            return;




        $.ajax({


            url:"/fees/"+id,


            type:"DELETE"



        })


        .done(function(){


            table.ajax.reload(null,false);


            showAlert(
                'Fee record deleted successfully',
                'warning'
            );


        })


        .fail(function(xhr){


            alert(xhr.responseJSON?.message ?? 'Delete error');


        });



    });




});

</script>
@endpush
