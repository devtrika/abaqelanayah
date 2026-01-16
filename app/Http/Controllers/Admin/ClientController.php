<?php
namespace App\Http\Controllers\Admin;

use App\Jobs\Notify;
use App\Models\City;
use App\Models\User;
use App\Jobs\SendSms;
use App\Models\Order;
use App\Mail\SendMail;
use App\Traits\Report;
use App\Models\Country;
use App\Models\District;
use App\Models\Complaint;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Imports\ClientImport;
use App\Notifications\BlockUser;
use App\Notifications\NotifyUser;
use App\Notifications\UnBlockUser;
use Illuminate\Support\Facades\DB;
use App\Exports\LoyaltyPointsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotifyRequest;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Admin\Client\Store;
use App\Http\Requests\Admin\Client\Update;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Admin\Client\BalanceRequest;

class ClientController extends Controller
{

    public function index($id = null)
    {
        if (request()->ajax()) {
            $searchArray = request()->searchArray;
            
            // Add district_id to search array if it exists in the request
            if (request()->has('district_id')) {
                $searchArray['district_id'] = request()->district_id;
            }
        if (request()->has('city_id')) {
                $searchArray['city_id'] = request()->city_id;
            }

            if (request()->has('is_active')) {
                $searchArray['is_active'] = request()->is_active;
            }
            $rows = User::with(['city', 'district', 'orders'])
                ->search($searchArray)
                ->where('type', 'client')
                ->paginate(30);
                
            $html = view('admin.clients.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.clients.index', ['district_id' => request('district_id') , 'city_id' => request('city_id') , 'is_active' => request('is_active')]);
    }

   public function create()
    {
        $cities = City::all();
        $districts = []; // Initially empty, will be populated via AJAX

        $countries = Country::all();
        
        return view('admin.clients.create', get_defined_vars());
    }

    public function store(Store $request)
    {
        $userData              = $request->validated();
        $userData['type']      = 'client'; // Ensure type is client
        $userData['is_active'] = 1;        // Ensure type is client

        $user = User::create($userData);

       

        Report::addToLog('إضافة عميل جديد');
        return response()->json(['url' => route('admin.clients.index')]);
    }


        public function activate(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->update(['is_active' => ! $user->is_active]);
        return response()->json(['message' => $user->refresh()->is_active == 1 ? __('admin.client_activated') : __('admin.client_deactivated')]);
    }

    public function edit($id)
    {
        $row = User::findOrFail($id);
        $cities = City::all();

        
        // Get districts for the user's city if they have one
        $districts = [];
        if ($row->city_id) {
            $districts = District::where('city_id', $row->city_id)->get();
        }

        $countries = Country::all();
                $regions = \App\Models\Region::all();


        // Get settings for default country
        $settings                    = [];
        $defaultCountrySetting       = SiteSetting::where('key', 'default_country')->first();
        $settings['default_country'] = $defaultCountrySetting ? $defaultCountrySetting->value : 1;

        return view('admin.clients.edit', get_defined_vars());
    }

    public function update(Update $request, $id)
    {
        $user = User::findOrFail($id);
       
        $user->update($request->validated());
        Report::addToLog('تعديل مستخدم');
        return response()->json(['url' => route('admin.clients.index')]);
    }

    /** public function Update Balance **/
    public function updateBalance(BalanceRequest $request)
    {
        $user   = User::findOrFail($request->user_id);
        $amount = convert2english($request->balance);
        DB::beginTransaction();
        try {
            if ($amount > 0) {
                (new TransactionService)->adminAddtoUserWallet($user, $amount);
            } elseif ($amount < 0) {
                (new TransactionService)->adminCutFromUserWallet($user, $amount);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return redirect()->back()->with('success', __('admin.update_successfullay'));
    }

    public function show($id)
    {
        $row = User::with([
            'city',
            'district',
            'addresses',
            'notifications',
        ])->findOrFail($id);

        if ($row->type !== 'client') {
            abort(404, 'Client not found');
        }

        // Get countries for the data tab
        $supported_countries = SiteSetting::where('key', 'countries')->first()->value ?? '';
        $supported_countries = json_decode($supported_countries);
        $countries           = Country::whereIn('id', $supported_countries)->orderBy('id', 'ASC')->get();

        return view('admin.clients.show', compact('row', 'countries'));
    }
    public function showfinancial($id)
    {
        $complaints = Complaint::where('user_id', $id)->paginate(10);
        return view('admin.complaints.user_complaints', ['complaints' => $complaints]);
    }

    public function showorders($id)
    {
        $orders = Order::where('user_id', $id)->paginate(10);
        return view('admin.clients.orders', ['orders' => $orders]);
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id)->delete();
        Report::addToLog('  حذف مستخدم');
        return response()->json(['id' => $id]);
    }

    public function block(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->update(['is_blocked' => ! $user->is_blocked]);
        Notification::send($user, new BlockUser($request->all()));
        return response()->json(['message' => $user->refresh()->is_blocked == 1 ? __('admin.client_blocked') : __('admin.client_unblocked')]);
    }

  public function notify(NotifyRequest $request)
    {
        if ($request->notify == 'notify') {
            if ('all' == $request->id) {
                $clients = User::where('type', 'client')->latest()->get();
                Notification::send($clients, new NotifyUser($request->all()));
            } else {
                $client = User::findOrFail($request->id);
                $client->notify(new NotifyUser($request->all()));
            }
        } elseif ($request->notify == 'email') {
            if ('all' == $request->id) {
                $mails = User::where('type', 'client')->where('email', '!=', null)->get()->pluck('email')->toArray();
            } else {
                $mails = User::findOrFail($request->id)->email;
            }
            Mail::to($mails)->send(new SendMail(['title' => 'اشعار اداري', 'message' => $request->message]));
        } elseif ($request->notify == 'sms') {
            if ('all' == $request->id) {
                $phones = User::where('phone', '!=', null)->get()->pluck('phone')->toArray();
                dispatch(new SendSms($phones, $request->body));
            } else {
                $phone = User::findOrFail($request->id)->full_phone;
                dispatch(new SendSms($phone, $request->body));
            }
        }
        return response()->json();
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);

        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (User::whereIntegerInRaw('id', $ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من المستخدمين');
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    public function importFile(Request $request)
    {
        Excel::import(new ClientImport, request()->file('file'));
        Report::addToLog(' رفع ملف بالعملاء');
        return response()->json(['url' => route('admin.clients.index')]);
    }

    public function loyalityPoints()
    {
        if (request()->ajax()) {
            $rows = User::with(['city', 'district', 'orders'])
                ->search(request()->searchArray)
                ->where('type', 'client')
                ->where('loyalty_points' , '>' , 0)
                ->paginate(30);
            $html = view('admin.clients.loyalitypoints.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.clients.loyalitypoints.index');
    }

    public function exportLoyaltyPoints()
{
    return Excel::download(
        new LoyaltyPointsExport,
        'loyalty_points_' . now()->format('Y-m-d_H-i') . '.xlsx'
    );
    }

}
