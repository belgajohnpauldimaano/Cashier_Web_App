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
        Route::post('tag-student-school-year', 'Admin\StudentController@tag_student_school_year')->name('admin.manage_student.tag_student_school_year');
        Route::post('save-tag-student-school-year', 'Admin\StudentController@save_tag_student_school_year')->name('admin.manage_student.save_tag_student_school_year'); 
    });
    Route::group(['prefix' => 'manage-fees'], function () {
        Route::get('{sy_id?}', 'Admin\ManageFeeController@index')->name('admin.manage_fees.index');
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

    Route::group(['prefix' => 'manage-school-years'], function () {
        Route::get('', 'Admin\SchoolYearController@index')->name('admin.manage_school_years.index');
        Route::post('list-data', 'Admin\SchoolYearController@list_data')->name('admin.manage_school_years.list_data');
        Route::post('modal-school-year', 'Admin\SchoolYearController@modal_school_year')->name('admin.manage_school_years.modal_school_year');
        Route::post('save-data', 'Admin\SchoolYearController@save_data')->name('admin.manage_school_years.save_data');
    });
    
    Route::group(['prefix' => 'student-tagged-school-year'], function () {
        Route::get('{sy_id?}', 'Admin\SchoolYearController@student_school_year_tagged')->name('admin.student_tagged_school_year.student_school_year_tagged');
        Route::post('form-modal', 'Admin\SchoolYearController@form_modal')->name('admin.student_tagged_school_year.form_modal');
        Route::post('save-data-student', 'Admin\SchoolYearController@save_data_student')->name('admin.student_tagged_school_year.save_data_student');
        Route::post('student-school-year-tagged-list_data', 'Admin\SchoolYearController@student_school_year_tagged_list_data')->name('admin.student_tagged_school_year.student_school_year_tagged_list_data');
        Route::post('deactivate-student', 'Admin\SchoolYearController@deactivate_student')->name('admin.student_tagged_school_year.deactivate_student');
        
    });

    Route::group(['prefix' => 'student-discount-list'], function () {
        Route::get('', 'Admin\StudentDiscountList@index')->name('admin.student_discount_list.index');
        Route::post('list-data', 'Admin\StudentDiscountList@list_data')->name('admin.student_discount_list.list_data');
        
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
        
        Route::post('edit-payment-modal', 'Reports\ReceivedPaymentsController@edit_payment_modal')->name('reports.receivedpayments.edit_payment_modal');
        Route::post('save-edit-entry', 'Reports\ReceivedPaymentsController@save_edit_entry')->name('reports.receivedpayments.save_edit_entry');
        Route::post('delete-entry', 'Reports\ReceivedPaymentsController@delete_entry')->name('reports.receivedpayments.delete_entry');
    });
    Route::group(['prefix' => 'monthly-payment-monitor'], function () {
        Route::get('', 'Reports\MonthlyPaymentMonitorController@index')->name('reports.monthly_payment_monitor.index');
        Route::post('list', 'Reports\MonthlyPaymentMonitorController@list')->name('reports.monthly_payment_monitor.list');
        Route::post('export-pdf-monthly-payment-monitor', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_monitor')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor');
        Route::post('export-pdf-monthly-payment-summary-monitor', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_summary_monitor')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_summary_monitor');
        Route::post('export-pdf-monthly-payment-monitor-teacher', 'Reports\MonthlyPaymentMonitorController@export_pdf_monthly_payment_monitor_teacher')->name('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor_teacher');
        
        
    });
    
});


