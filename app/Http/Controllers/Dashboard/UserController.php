<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_create'])->only('create');
        $this->middleware(['permission:users_update'])->only('edit');
        $this->middleware(['permission:users_delete'])->only('destroy');

    }

    public function index(Request $request)
    {

        $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');

            });

        })->latest()->paginate(3);

        return view('Dashboard.users.index', compact('users'));

    }


    public function create()
    {
        return view('dashboard.users.create');
    }


    public function store(Request $request)
    {
        $request->validate([

            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
            'permissions' => 'required|min:1'


        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);

        $request_data['password'] = bcrypt($request->password);

        if ($request->image) {

          Image::make($request->image)->resize(300, null, function($constraint){

            $constraint->aspectRatio();

          })
          ->save(public_path('uploads/avatars/' . $request->image->hashName()));

          $request_data['image'] = $request->image->hashName();

        } //end of iamge

        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.add_successfully'));

        return redirect(route('dashboard.users.index'));
    }

    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
      $request->validate([

          'first_name' => 'required',
          'last_name' => 'required',
          'email' => [
            'required',
            Rule::unique('users')->ignore($user->id),
          ],
          'image' => 'required|image',
          'permissions' => 'required|min:1'

      ]);

      $request_data = $request->except(['permissions', 'image']);

      if ($request->image) {

          if ($user->image != 'default.jpg') {

            Storage::disk('public_uploads')->delete('/avatars/' . $user->image);

          }

          Image::make($request->image)->resize(300, null, function($constraint){

            $constraint->aspectRatio();

          })
          ->save(public_path('uploads/avatars/' . $request->image->hashName()));

          $request_data['image'] = $request->image->hashName();


      } //end of iamge

      $user->update($request_data);

      $user->syncPermissions($request->permissions);

      session()->flash('success', __('site.updated_successfully'));

      return redirect(route('dashboard.users.index'));

    }


    public function destroy(User $user)
    {
        if ($user->image != 'default.jpg') {

          Storage::disk('public_uploads')->delete('/avatars/' . $user->image);
        }

        $user->delete();

        session()->flash('success', ('site.deleted_successfully'));

        return redirect(route('dashboard.users.index'));
    }
}
