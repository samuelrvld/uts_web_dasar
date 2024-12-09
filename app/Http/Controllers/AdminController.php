<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function AdminLogin()
    {
        return view('admin.login');
    }
    public function AdminDashboard()
    {
        return view('admin.index');
    }
    public function AdminLoginSubmit(Request $request)
    {
        // $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt( $request->only('email', 'password'))) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'Email atau password salah.']);
        }


    }
    public function AdminLogout(){
        Auth::guard('admin')->logout();
        return redirect('/');
    }
    public function ForgetPassword(){
        return view('admin.forgetPassword');
    }
    public function passwordGenerate(Request $request){
        return 'Hello world';
    }
    public function adminProfile(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.profile',compact('profileData'));
    }
    public function changePassword(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.change_password',compact('profileData'));
    }
    public function AdminProfileStore(Request $request)
    {
        $id = Auth::guard('admin')->id();
        $data = Admin::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/admin_images'), $fileName);
            $data->photo = $fileName;
        }

        if ($oldPhotoPath && $oldPhotoPath !== $fileName) {
            $this->deleteOldImage($oldPhotoPath);
        }

        $data->save();
        return redirect()->back();
    }

    private function deleteOldImage(string $oldPhotoPath): void
    {
        $fullPath = public_path('upload/admin_images/' . $oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
