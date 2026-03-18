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
    $request->validate([
        'domain' => 'required|unique:domains,domain',
        // 'php_version' => 'required' // Có thể bỏ validate này nếu muốn hoàn toàn auto
    ]);

    // Ép kiểu luôn về 8.4 hoặc lấy từ request nếu sau này muốn mở lại
    $phpVersion = $request->php_version ?? '8.4'; 
    $rootPath = '/var/www/' . $request->domain;

    Domain::create([
        'domain' => $request->domain,
        'agent_key' => Str::random(32),
        'root_path' => $rootPath,
        'php_version' => $phpVersion, // Sẽ luôn là 8.4
        'status' => 'pending_setup'
    ]);

    return back()->with('success', 'Domain added successfully with PHP 8.4!');
}

    // Hàm mới để xử lý Xóa
    public function destroy($id)
    {
        $domain = Domain::findOrFail($id);
        $domain->delete();
        
        return back()->with('success', 'Domain deleted successfully!');
    }
    public function toggle($id) {
        $domain = Domain::findOrFail($id);
        $domain->is_active = !($domain->is_active ?? true);
        $domain->save();
        return back()->with('success', 'Status updated!');
    }
}