<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::latest()->get();
        return view('domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'domain' => 'required|unique:domains,domain',
            'php_version' => 'required'
        ]);

        $value = $request->domain;
        $type = filter_var($value, FILTER_VALIDATE_IP) ? 'ip' : 'domain';
        
        // 2. Tự động sinh Root Path
        $rootPath = '/var/www/' . $request->domain;

        // 3. Lưu vào Database
        Domain::create([
            'domain' => $request->domain,
            'agent_key' => Str::random(32),
            'root_path' => $rootPath,
            'php_version' => $request->php_version,
            'status' => 'pending_setup' // Đổi status để nhận diện web mới
        ]);

        return back()->with('success', 'Domain added successfully!');
    }

    // Hàm mới để xử lý Xóa
    public function destroy($id)
    {
        $domain = Domain::findOrFail($id);
        $domain->delete();
        
        return back()->with('success', 'Domain deleted successfully!');
    }
}