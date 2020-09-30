<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;

class ClientController extends Controller
{

    public function __construct()
    {

        $this->middleware(['permission:clients_read'])->only('index');
        $this->middleware(['permission:clients_create'])->only('create');
        $this->middleware(['permission:clients_update'])->only('edit');
        $this->middleware(['permission:clients_delete'])->only('destroy');

    }

    public function index(Request $request)
    {
        $clients = Client::when($request->search, function($q) use ($request) {

            return $q->where('name', 'like', '%' . $request->search . '%')
                ->OrWhere('phone', 'like', '%' . $request->search . '%')
                ->OrWhere('address', 'like', '%' . $request->search . '%');

        })->latest()->paginate(10);

        return view('dashboard.clients.index', compact('clients'));
    }


    public function create()
    {
        return view('dashboard.clients.create');
    }


    public function store(Request $request)
    {
        $request->validate([
          'name' => 'required',
          'phone' => 'required|array|min:1',
          'phone.0' => 'required',
          'address' => 'required',
        ]);

        $data = $request->all();
        $data['phone'] = array_filter($request->phone);

        Client::create($data);
        session()->flash('success', __('site.add_successfully'));

        return redirect(route('dashboard.clients.index'));
    }


    public function show($id)
    {
        //
    }


    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }


    public function update(Request $request, Client $client)
    {
      $request->validate([
        'name' => 'required',
        'phone' => 'required|array|min:1',
        'phone.0' => 'required',
        'address' => 'required',
      ]);

      $data = $request->all();
      $data['phone'] = array_filter($request->phone);

      $client->update($data);
      session()->flash('success', __('site.updated_successfully'));

      return redirect(route('dashboard.clients.index'));
    }


    public function destroy(Client $client)
    {
        $client->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect(route('dashboard.clients.index'));

    }
}
