<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TrafficLog;
use App\Models\BlockedIP;
use App\Models\SecurityEvent;
use App\Models\Domain;

class AIController extends Controller
{
    public function chat(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        // ✅ FIX 1: check login
        if (!$user) {
            return response()->json([
                'reply' => "⚠️ Please login first."
            ]);
        }

        $message = trim($data['message']);

        if (strlen($message) > 200) {
            return response()->json([
                'reply' => "⚠️ Message too long."
            ]);
        }

        $intent = $this->detectIntent($message);

        try {
            switch ($intent) {
                case 'top_ip':
                    return $this->handleTopIP($user);

                case 'blocked_ip':
                    return $this->handleBlockedIP();

                case 'security':
                    return $this->handleSecurityEvents();

                case 'traffic':
                    return $this->handleTrafficSummary();

                case 'my_domains':
                    return $this->handleUserDomains($user);

                default:
                    return $this->handleAIResponse($message);
            }

        } catch (\Exception $e) {
            // ✅ FIX 2: debug rõ lỗi
            return response()->json([
                'reply' => "❌ Server error: " . $e->getMessage()
            ]);
        }
    }

    // =========================
    // 🧠 INTENT DETECTION
    // =========================
    private function detectIntent($message)
    {
        $msg = strtolower($message);

        if (
            (str_contains($msg, 'top') && str_contains($msg, 'ip')) ||
            str_contains($msg, 'most active') ||
            str_contains($msg, 'highest traffic')
        ) return 'top_ip';

        if (str_contains($msg, 'blocked') || str_contains($msg, 'ban')) return 'blocked_ip';

        if (
            str_contains($msg, 'attack') ||
            str_contains($msg, 'security') ||
            str_contains($msg, 'threat')
        ) return 'security';

        if (
            str_contains($msg, 'traffic') ||
            str_contains($msg, 'requests')
        ) return 'traffic';

        if (
            str_contains($msg, 'my domain') ||
            str_contains($msg, 'my website')
        ) return 'my_domains';

        return 'unknown';
    }

    // =========================
    // 📊 TOP IP (FIXED)
    // =========================
    private function handleTopIP($user)
    {
        $domains = Domain::where('user_id', $user->id)->pluck('domain');

        if ($domains->isEmpty()) {
            return response()->json([
                'reply' => "📭 You don't have any domains yet."
            ]);
        }

        // ✅ FIX: dùng domain thay vì domain_id
        $ips = TrafficLog::whereIn('domain', $domains)
            ->selectRaw('ip, count(*) as total')
            ->groupBy('ip')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($ips->isEmpty()) {
            return response()->json([
                'reply' => "📭 No traffic data yet."
            ]);
        }

        $text = "📊 Top IPs:\n\n";

        foreach ($ips as $ip) {
            $text .= "• {$ip->ip} → {$ip->total} requests\n";
        }

        return response()->json(['reply' => $text]);
    }

    // =========================
    // 🚫 BLOCKED IP
    // =========================
    private function handleBlockedIP()
    {
        $blocked = BlockedIP::latest()->limit(10)->get();

        if ($blocked->isEmpty()) {
            return response()->json([
                'reply' => "✅ No blocked IPs."
            ]);
        }

        $text = "🚫 Blocked IPs:\n\n";

        foreach ($blocked as $b) {
            $reason = $b->reason ? " ({$b->reason})" : "";
            $text .= "• {$b->ip}{$reason}\n";
        }

        return response()->json(['reply' => $text]);
    }

    // =========================
    // ⚠️ SECURITY EVENTS
    // =========================
    private function handleSecurityEvents()
    {
        $events = SecurityEvent::latest()->limit(10)->get();

        if ($events->isEmpty()) {
            return response()->json([
                'reply' => "🛡️ No threats detected."
            ]);
        }

        $text = "⚠️ Security events:\n\n";

        foreach ($events as $event) {
            $text .= "• {$event->created_at->format('H:i')} - {$event->type}\n";
        }

        return response()->json(['reply' => $text]);
    }

    // =========================
    // 📈 TRAFFIC
    // =========================
    private function handleTrafficSummary()
    {
        $total = TrafficLog::count();

        return response()->json([
            'reply' => "📈 Total traffic: {$total} requests"
        ]);
    }

    // =========================
    // 🌐 USER DOMAINS
    // =========================
    private function handleUserDomains($user)
    {
        $domains = Domain::where('user_id', $user->id)->get();

        if ($domains->isEmpty()) {
            return response()->json([
                'reply' => "🌐 No domains found."
            ]);
        }

        $text = "🌐 Your domains:\n\n";

        foreach ($domains as $d) {
            $text .= "• {$d->domain}\n";
        }

        return response()->json(['reply' => $text]);
    }

    // =========================
    // 🤖 OPENAI
    // =========================
    private function handleAIResponse($message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a cybersecurity assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'max_tokens' => 150,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'reply' => $response['choices'][0]['message']['content']
                ]);
            }

            return response()->json([
                'reply' => "⚠️ AI not responding."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => "❌ AI error: " . $e->getMessage()
            ]);
        }
    }
}