<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Officer\TindakLanjutController;

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


Route::group(['middleware' => 'auth'], function () {
		Route::get('/admin/tickets/{id}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->middleware('auth')->name('admin.tickets.show');
	Route::get('/admin/tickets', [TicketController::class, 'index'])->middleware('auth')->name('admin.tickets');
    Route::post('/admin/tickets', [TicketController::class, 'store'])->middleware('auth');

	Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		$role = auth()->user()->role;
		if ($role === 'admin') {
			return redirect('/admin/dashboard');
		} elseif ($role === 'officer') {
			return redirect('/officer/dashboard');
		} elseif ($role === 'qa') {
			return redirect('/qa/dashboard');
		}
		return view('dashboard');
	})->name('dashboard');

	Route::get('/admin/dashboard', function () {
		return view('admin.dashboard');
	})->middleware('auth')->name('admin.dashboard');

	Route::get('/officer/dashboard', function () {
		return view('officer.dashboard');
	})->middleware('auth')->name('officer.dashboard');

	Route::get('/qa/dashboard', function () {
		return view('qa.dashboard');
	})->middleware('auth')->name('qa.dashboard');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');

	Route::get('user-management', function () {
		return view('laravel-examples/user-management');
	})->name('user-management');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

    Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');
Route::group(['middleware' => 'auth'], function () {
    // Tindak lanjut admin
    Route::match(['get', 'post'], '/admin/tindak-lanjut', [TicketController::class, 'tindakLanjut'])->name('admin.tindak-lanjut');

    // API: search officers by name (used by admin assign UI)
    Route::get('/admin/officers', function(Illuminate\Http\Request $request) {
        $q = $request->input('q');
        $query = \App\Models\User::where('role', 'officer');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }
        $users = $query->limit(20)->get(['id','name','email']);
        return response()->json($users);
    })->name('admin.officers');

    // Tindak lanjut officer
    Route::get('/officer/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('officer.tindak-lanjut');
    Route::post('/officer/tindak-lanjut', [TindakLanjutController::class, 'proses'])->name('officer.tindak-lanjut.proses');

    // Tindak lanjut qa (GET & POST — QA summary + status update)
    Route::match(['get','post'], '/qa/tindak-lanjut', function(Request $request) {
        $ticket = null;
        $canResolve = false;
        $assignedOfficers = collect();

        if ($request->filled('nomor_tiket')) {
            $ticket = \App\Models\Ticket::where('nomor_tiket', $request->input('nomor_tiket'))->first();
            if ($ticket) {
                // load pivot officer records
                $assignedOfficers = \DB::table('ticket_officer')
                    ->where('ticket_id', $ticket->id)
                    ->get();

                // canResolve true if there is at least one assigned officer and ALL have status 'proses_qa'
                if ($assignedOfficers->isNotEmpty()) {
                    $canResolve = $assignedOfficers->every(fn($r) => $r->status === 'proses_qa');
                } else {
                    $canResolve = false;
                }
            }
        }

        if ($request->isMethod('post')) {
            if (! $ticket) {
                return back()->with('error', 'Tiket tidak ditemukan.');
            }

            // save QA summary if provided
            if ($request->filled('qa_summary')) {
                $ticket->qa_summary = $request->input('qa_summary');
                $ticket->save();

                // log QA update
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket->id,
                    'action' => 'qa_summary_updated',
                    'detail' => 'QA updated summary: ' . \Illuminate\Support\Str::limit($ticket->qa_summary ?? '', 300),
                    'ip' => $request->ip(),
                ]);
            }

            // handle status update
            if ($request->filled('status')) {
                $newStatus = $request->input('status');
                // re-check canResolve if resolving
                if ($newStatus === 'resolved') {
                    $assignedOfficers = \DB::table('ticket_officer')->where('ticket_id', $ticket->id)->get();
                    $canResolve = $assignedOfficers->isNotEmpty() && $assignedOfficers->every(fn($r) => $r->status === 'proses_qa');
                    if (! $canResolve) {
                        return back()->with('error', 'Semua officer harus berstatus proses_qa sebelum tiket dapat diselesaikan.');
                    }
                }

                // perform status change and log it
                if (in_array($newStatus, ['on_progress', 'resolved'])) {
                    $oldStatus = $ticket->status;
                    $ticket->status = $newStatus === 'on_progress' ? 'in_progress' : 'resolved';
                    $ticket->save();

                    \App\Models\ActivityLog::create([
                        'user_id' => auth()->id(),
                        'ticket_id' => $ticket->id,
                        'action' => 'status_changed_by_qa',
                        'detail' => "Status: {$oldStatus} -> {$ticket->status}",
                        'ip' => $request->ip(),
                    ]);
                }
            }

            return back()->with('success', 'Perubahan QA berhasil disimpan.')->withInput();
        }

        return view('qa.tindak-lanjut', compact('ticket','assignedOfficers','canResolve'));
    })->middleware('auth')->name('qa.tindak-lanjut');

    // Admin: edit/update/delete ticket
    Route::get('/admin/tickets/{id}/edit', [TicketController::class, 'edit'])->middleware('auth')->name('admin.tickets.edit');
    Route::put('/admin/tickets/{id}', [TicketController::class, 'update'])->middleware('auth')->name('admin.tickets.update');
    Route::delete('/admin/tickets/{id}', [TicketController::class, 'destroy'])->middleware('auth')->name('admin.tickets.destroy');
    
    // Admin
    Route::get('/admin/tickets', [TicketController::class, 'index'])->middleware('auth')->name('admin.tickets');
    Route::post('/admin/tickets', [TicketController::class, 'store'])->middleware('auth');

    // QA: Daftar Tiket (lihat semua tiket)
    Route::get('/qa/tickets', function(Request $request) {
        $query = \App\Models\Ticket::query();
        // Filter (duplikat dari admin)
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($sub) use ($q) {
                $sub->where('nomor_tiket', 'like', "%$q%")
                    ->orWhere('nama_pelapor', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('judul', 'like', "%$q%")
                    ->orWhere('detail', 'like', "%$q%")
                    ->orWhere('officer', 'like', "%$q%")
                    ->orWhere('kategori', 'like', "%$q%")
                    ->orWhere('status', 'like', "%$q%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }
        $tickets = $query->orderBy('created_at', 'desc')->paginate(7)->withQueryString();
        return view('qa.tickets', compact('tickets'));
    })->middleware('auth')->name('qa.tickets');

    // Officer: Daftar Tiket (hanya tiket yang diassign)
    Route::get('/officer/tickets', function(Request $request) {
        $user = auth()->user();
        $query = \App\Models\Ticket::query();
        // Officer hanya tiket yang diassign (nama officer ada di kolom officer)
        $query->where(function($q) use ($user) {
            $q->where('officer', 'like', "%$user->name%");
        });
        // Filter (duplikat dari admin)
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($sub) use ($q) {
                $sub->where('nomor_tiket', 'like', "%$q%")
                    ->orWhere('nama_pelapor', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('judul', 'like', "%$q%")
                    ->orWhere('detail', 'like', "%$q%")
                    ->orWhere('officer', 'like', "%$q%")
                    ->orWhere('kategori', 'like', "%$q%")
                    ->orWhere('status', 'like', "%$q%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }
        $tickets = $query->orderBy('created_at', 'desc')->paginate(7)->withQueryString();
        return view('officer.tickets', compact('tickets'));
    })->middleware('auth')->name('officer.tickets');

    // Chat API (server-side)
    Route::get('/chat/messages/{nomor_tiket}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/messages', [ChatController::class, 'send'])->name('chat.send');
});