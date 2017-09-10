<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'manage-student'], function () {
        Route::get('', 'Admin\StudentController@index')->name('admin.manage_student.index');
        Route::post('form-modal', 'Admin\StudentController@form_modal')->name('admin.manage_student.form_modal');
        Route::post('save-data', 'Admin\StudentController@save_data')->name('admin.manage_student.save_data');
        Route::post('list', 'Admin\StudentController@list')->name('admin.manage_student.list');
        Route::post('delete', 'Admin\StudentController@delete')->name('admin.manage_student.delete');
        Route::get('formulate_sy', 'Admin\StudentController@formulate_sy')->name('admin.manage_student.formulate_sy');
    });
    Route::group(['prefix' => 'manage-fees'], function () {
        Route::get('', 'Admin\ManageFeeController@index')->name('admin.manage_fees.index');
        Route::post('form-modal', 'Admin\ManageFeeController@form_modal')->name('admin.manage_fees.form_modal');
        Route::post('save_data', 'Admin\ManageFeeController@save_data')->name('admin.manage_fees.save_data');
        Route::post('list', 'Admin\ManageFeeController@list')->name('admin.manage_fees.list');
        
    });
    Route::group(['prefix' => 'manage-discounts'], function () {
        Route::get('', 'Admin\ManageDiscountsController@index')->name('admin.manage_discounts.index');
        Route::post('list', 'Admin\ManageDiscountsController@list')->name('admin.manage_discounts.list');
        Route::post('form-modal', 'Admin\ManageDiscountsController@form_modal')->name('admin.manage_discounts.form_modal');
        Route::post('save-data', 'Admin\ManageDiscountsController@save_data')->name('admin.manage_discounts.save_data');
        
    });
});

        Route::get('test_data', 'Admin\StudentController@test_data')->name('admin.manage_student.test_data');


Route::group(['prefix' => 'cashier', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'student-payment'], function () {
        Route::get('', 'Cashier\StudentPaymentController@index')->name('cashier.student_payment.index');
        Route::post('show-form-modal-pay-tuition', 'Cashier\StudentPaymentController@show_form_modal_pay_tuition')->name('cashier.student_payment.show_form_modal_pay_tuition');
        Route::post('tuition-payment-process', 'Cashier\StudentPaymentController@tuition_payment_process')->name('cashier.student_payment.tuition_payment_process');
        Route::post('fetch-data', 'Cashier\StudentPaymentController@fetch_data')->name('cashier.student_payment.fetch_data'); 
        Route::post('show-form-modal-additional-payment', 'Cashier\StudentPaymentController@show_form_modal_additional_payment')->name('cashier.student_payment.show_form_modal_additional_payment'); 
        Route::post('additional-fee-payment-process', 'Cashier\StudentPaymentController@additional_fee_payment_process')->name('cashier.student_payment.additional_fee_payment_process');
        Route::post('student-summary-balance', 'Cashier\StudentPaymentController@student_summary_balance')->name('cashier.student_payment.student_summary_balance');
        Route::post('student-summary-simple-balance', 'Cashier\StudentPaymentController@student_summary_simple_balance')->name('cashier.student_payment.student_summary_simple_balance');
        
    });
    Route::group(['prefix' => 'student-additional-payment'], function () {
        Route::get('', 'Cashier\StudentAdditionalPaymentController@index')->name('cashier.student_additional_payment.index');
        Route::post('list-data', 'Cashier\StudentAdditionalPaymentController@list_data')->name('cashier.student_additional_payment.list_data');
        Route::post('form-modal-additional-payment', 'Cashier\StudentAdditionalPaymentController@form_modal_additional_payment')->name('cashier.student_additional_payment.form_modal_additional_payment');
        Route::post('process-payment', 'Cashier\StudentAdditionalPaymentController@process_payment')->name('cashier.student_additional_payment.process_payment');
        Route::post('student-additional-fee-report', 'Cashier\StudentAdditionalPaymentController@student_additional_fee_report')->name('cashier.student_additional_payment.student_additional_fee_report');
        
    });

});

Route::group(['prefix' => 'reports', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'received-payment'], function () {
        Route::get('', 'Reports\ReceivedPaymentsController@index')->name('reports.receivedpayments.index');
        Route::post('list', 'Reports\ReceivedPaymentsController@list')->name('reports.receivedpayments.list');
        Route::post('export-pdf-received-payments', 'Reports\ReceivedPaymentsController@export_pdf_received_payments')->name('reports.receivedpayments.export_pdf_received_payments');
        
        Route::post('received-payments-summary-report', 'Reports\ReceivedPaymentsController@received_payments_summary_report')->name('reports.receivedpayments.received_payments_summary_report');
        
    });
    Route::group(['prefix' => 'monthly-payment-monitor'], function () {
        Route::get('', 'Reports\MonthlyPaymentMonitorController@index')->name('reports.monthly_payment_monitor.index');
        Route::post('list', 'Reports\MonthlyPaymentMonitorController@list')->name('reports.monthly_payment_monitor.list');
        Route::post('export-pdf-monthly-payment-monitor', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_monitor')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor');
        Route::post('export-pdf-monthly-payment-summary-monitor', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_summary_monitor')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_summary_monitor');
        Route::post('export-pdf-monthly-payment-monitor-teacher', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_monitor_teacher')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor_teacher');
        
        
    });
    
});


