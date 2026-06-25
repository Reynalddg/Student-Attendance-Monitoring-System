<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSLogController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\StrandController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\StudentAttendanceViewController;


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
    return view('homepage');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/teacherdashboard', function(){
    return view('teacherDashboard');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/scan', function () {
    return view('scanAttendance.scan');
});

Route::get('/homepage', function () {
    return view('homepage');
})->name('homepage');

Route::get('/logout', function () {
    return view('auth.login');
});

//Student Crud
Route::controller(StudentController::class)->group( function(){
Route::get('/student', 'index')->name('students')->middleware('auth');
Route::get('/add/student', 'create');
Route::post('/add/student', 'store');
Route::put('/student/{id}','update')->name('student.update');
Route::delete('/student/{id}', 'destroy')->name('student.destroy');
Route::get('/students/search', 'search')->name('students.search');
Route::get('/teacher/studentView', 'studentView')->name('teacher.studentView')->middleware('auth');
Route::get('/students/searchStudent', 'searchStudent')->name('students.studentSearch');
Route::get('/teacher/export-sf1',  'exportSF1')->name('sf1.export.excel');
Route::get('/teacher/export-sf1-pdf', 'exportSF1PDF')->name('sf1.export.pdf');
Route::get('/students/archived', 'archived')->name('students.archived');
Route::put('/students/{id}/restore',  'restore')->name('students.restore');
Route::get('/students/archived/search', 'archivedSearch')->name('students.archived.search');
Route::post('/students/import','import')->name('students.import');


});


//Users Crud
Route::controller(UserController::class)->group( function(){
Route::get('/users', 'index')->name('users')->middleware('auth');
Route::post('/add/user', 'store');
Route::put('/user/{id}', 'update')->name('user.update');
Route::delete('user/{id}', 'destroy')->name('user.destroy');
Route::post('/logout','logout');
Route::post('/login/process', 'process');
Route::post('/users/change-password','changePassword')->name('users.changePassword');
Route::get('/users/search', 'search')->name('users.search');
Route::get('/users/archived', 'archived')->name('users.archived');
Route::put('/users/{id}/restore',  'restore')->name('users.restore');
Route::get('/users/archived/search', 'archivedSearch')->name('users.archived.search');

});

//Track Crud
Route::controller(TrackController::class)->group( function(){
Route::get('/tracks', 'index')->name('tracks')->middleware('auth');
Route::post('/add/track', 'store');
Route::put('/track/{id}', 'update')->name('track.update');
Route::delete('/track/{id}', 'destroy')->name('track.destroy');
Route::get('/tracks/search', 'search')->name('tracks.search');
Route::get('/tracks/archived/search', 'archivedSearch')->name('tracks.archived.search');
Route::get('/tracks/archived', 'archived')->name('tracks.archived');
Route::put('/tracks/{id}/restore',  'restore')->name('tracks.restore');
});

//Strand Crud
Route::controller(StrandController::class)->group(function(){
Route::get('/strands', 'index')->name('strands')->middleware('auth');
Route::post('/add/strand', 'store');
Route::put('/strand/{id}', 'update')->name('strand.update');
Route::delete('/strand/{id}', 'destroy')->name('strand.destroy');
Route::get('/strands/search',  'search')->name('strands.search');
});

//section crud
Route::controller(SectionController::class)->group(function(){
Route::get('/sections', 'index')->name('sections')->middleware('auth');
Route::post('/add/section', 'store');
Route::put('/section/{id}', 'update')->name('section.update');
Route::delete('/section/{id}', 'destroy')->name('section.destroy');
Route::get('/sections/search', 'search')->name('sections.search');
Route::get('/sections/archived/search', 'archivedSearch')->name('sections.archived.search');
Route::get('/sections/archived', 'archived')->name('sections.archived');
Route::put('/sections/{id}/restore',  'restore')->name('sections.restore');
});

//academic Year Crud
Route::controller(AcademicYearController::class)->group( function(){
Route::get('/academicYears', 'index')->name('academicYears')->middleware('auth');
Route::post('/add/academicYear', 'store');
Route::put('/academicYear/{id}', 'update')->name('academicYear.update');
Route::delete('/academicYear/{id}', 'destroy')->name('academicYear.destroy');
Route::get('/academicYears/search', 'search')->name('academicYears.search');
Route::get('/academicYears/archived', 'archived')->name('academicYears.archived');
Route::put('/academicYears/{id}/restore',  'restore')->name('academicYears.restore');
Route::get('/academicYears/archived/search', 'archivedSearch')->name('academicYears.archived.search');
});

//semester crud
Route::controller(SemesterController::class)->group(function(){
Route::get('/semesters', 'index')->name('semesters')->middleware('auth');
Route::post('/add/semester', 'store');
Route::put('/semester/{id}', 'update')->name('semester.update');
Route::delete('/semester/{id}', 'destroy')->name('semester.destroy');
Route::get('/semesters/search', 'search')->name('semesters.search');
Route::get('/semesters/archived', 'archived')->name('semesters.archived');
Route::put('/semesters/{id}/restore',  'restore')->name('semesters.restore');
Route::get('/semesters/archived/search', 'archivedSearch')->name('semesters.archived.search');
});

// 🔹 Adviser CRUD (pang-admin)
Route::controller(AdviserController::class)->group(function(){
    Route::get('/advisers', 'index')->name('advisers')->middleware('auth');
    Route::post('/add/adviser', 'store');
    Route::put('/adviser/{id}', 'update')->name('adviser.update');
    Route::delete('/adviser/{id}', 'destroy')->name('adviser.destroy');
    Route::get('/advisers/search', 'search')->name('advisers.search');
    Route::get('/advisers/archived/search',  'archivedSearch')->name('advisers.archived.search');
    Route::get('/advisers/archived', 'archived')->name('advisers.archived');
    Route::put('/advisers/{id}/restore',  'restore')->name('advisers.restore');
});
// 🔹 Adviser (Teacher) Dashboard Routes
Route::middleware(['auth'])->group(function() {
    Route::get('/teacher/dashboard', [App\Http\Controllers\TeacherDashboardController::class, 'index'])
        ->name('teacher.teacherDashboard');
});

//guardian Crud
Route::controller(GuardianController::class)->group(function(){
    Route::get('/guardians', 'index')->name('dashboard.guardians')->middleware('auth');
    Route::post('/add/guardian', 'store');
    Route::put('/guardian/{id}', 'update')->name('guardian.update');
    Route::delete('/guardian/{id}', 'destroy')->name('guardian.destroy');
    Route::get('/guardian/search', 'search')->name('guardians.guardianSearch');
    Route::get('/teacher/guardianView', 'guardianView')->name('teacher.guardianView');
    Route::get('/teacher/guardians/search', 'searchGuardian')->name('guardians.search');
    Route::get('/guardians/archived', 'archived')->name('guardians.archived');
    Route::put('/guardians/{id}/restore',  'restore')->name('guardians.restore');
    Route::get('/guardians/archived/search', 'archivedSearch')->name('guardians.archived.search');


});

//Student Enrollment Crud
Route::controller(StudentEnrollmentController::class)->group(function(){
    Route::get('/studentEnrollments', 'index')->name('teacher.studentEnrollments')->middleware('auth');
    Route::post('/add/studentEnrollment', 'store');
    Route::put('/studentEnrollment/{id}', 'update')->name('studentEnrollment.update');
    Route::get('/studentEnrollment/search', 'search')->name('studentEnrollment.search');
    Route::get('/studentEnrollment/searchByLRN',  'searchByLRN');
    Route::post('/studentEnrollment/import', 'import')->name('studentEnrollment.import');


});


//Attendance 
Route::controller(AttendanceLogController::class)->group(function(){
Route::post('/add/attendance','store')->name('attendance.store');
Route::get('/attendances',  'index')->name('attendances')->middleware('auth');
Route::get('/attendance/filter', 'filter')->name('attendance.filter');
Route::post('/add/adminAtt',  'adminAttStore');
Route::put('/update/adminAtt/{id}',  'update')->name('attendance.update');
});

//SMS Routes
Route::controller(SMSLogController::class)->group(function(){
Route::get('/send-sms-all', 'sendSMSAll')->name('send.sms.all')->middleware('auth');
Route::get('/send-sms/{studentId}', 'sendTestSMS')->name('send.sms');
Route::post('/teacher/send-sms-guardians', 'sendSMSFromAdviser')->name('sms.sendFromAdviser');
Route::get('/smsLogs', 'index')->name('smsLogs');
});

//Teacher Dashboard Routes
Route::middleware(['web'])->group(function () {
    Route::controller(TeacherDashboardController::class)->group(function(){
        Route::get('/teacher/dashboard','index')->name('teacher.teacherDashboard')->middleware('auth');
    });
});

//Attendance View Crud

    Route::controller(StudentAttendanceViewController::class)->group(function(){
        Route::get('/teacher/export-attendance', 'exportMonthlyAttendance')->name('teacher.export.attendance');
        Route::get('/teacher/attendanceView', 'index')->name('teacher.attendanceView')->middleware('auth');
        Route::post('/add/studentAtt',  'store');
        Route::put('/update/studentAtt/{id}', 'update')->name('attendance.update');
        Route::get('/teacher/get-semester', 'getSemester');
        Route::get('/teacher/export-sf2-pdf', 'downloadSF2PDF')->name('sf2.export.pdf');

        });




Route::get('/attendance', function () {
    return view('dashboard.attendance');
})->name('attendance');




Route::get('/adminAcc', function () {
    return view('dashboard.adminAcc');
})->name('adminAcc');


Route::get('/attendanceView', function(){
    return view('teacher.attendanceview');
})->name('attendanceView');